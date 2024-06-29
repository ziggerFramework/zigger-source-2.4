<?php
namespace Module\Recentposts;

use Corelib\Func;
use Corelib\Session;
use Corelib\Valid;
use Corelib\Method;
use Make\Library\Uploader;
use Make\Database\Pdosql;
use Module\Recentposts\Library as Recentposts_Library;
use Module\Board\Library as Board_Library;

//
// Module Controller
// ( Set_recent )
//
class Set_recent {

    public function init()
    {
        global $req, $moduleconf, $MOD_CONF;

        $sql = new Pdosql();
        $sess = new Session();

        $req = Method::request('get', 'mode, read');

        $modulelib = new Recentposts_Library();
        $moduleconf = $modulelib->load_conf();
        $moduleconf = explode('|', $moduleconf['boards']);
        
        // 허용된 게시판만 recent posts 기록
        if (!in_array($MOD_CONF['id'], $moduleconf)) return;

        // 읽기 화면인 경우 view +1 처리
        if ($req['mode']=='view' && $req['read']) {

            // load session
            $view_sess = $sess->sess('RECENTPOSTS_VIEW_'.$req['read']);

            if (isset($view_sess)) return;

            $sql->query(
                "
                update {$sql->table("mod:recentposts")} set
                view = view + 1
                where board_id=:col1 and bo_idx=:col2
                ",
                array(
                    $MOD_CONF['id'], $req['read']
                )
            );

            $sess->set_sess('RECENTPOSTS_VIEW_'.$req['read'], $req['read']);
            
        }
       
    }

}

//
// Module Controller
// ( Set_recent_write_submit )
//
class Set_recent_write_submit {

    public function init()
    {
        global $req, $moduleconf;

        $sql = new Pdosql();

        $req = Method::request('post', 'board_id, wrmode, read, subject, article, s_password');

        $modulelib = new Recentposts_Library();
        $moduleconf = $modulelib->load_conf();
        $moduleconf = explode('|', $moduleconf['boards']);

        // 허용된 게시판만 recent posts 기록
        if (!in_array($req['board_id'], $moduleconf)) return;

        // route
        switch ($req['wrmode']) {

            case 'modify' :
                $this->get_modify();
                break;

            case 'delete' :
                $this->get_delete();
                break;

            default :
                if ($req['wrmode'] != 'reply') $this->get_write();
        }

    }

    //
    // write
    //
    public function get_write()
    {
        global $req;

        $sql = new Pdosql();

        // 신규 등록된 게시글 정보를 가져옴
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".$req['board_id'])}
            where subject=:col1 and article=:col2
            ",
            array(
                $req['subject'], $req['article']
            )
        );
        $sql->specialchars = 0;
        $sql->nl2br = 0;
        $arr = $sql->fetchs();

        if (!$arr['idx']) return;

        // recent posts 기록
        $sql->query(
            "
            select *
            from {$sql->table("mod:recentposts")}
            where board_id=:col1 and bo_idx=:col2
            ",
            array(
                $req['board_id'], $arr['idx']
            )
        );
        if ($sql->getcount() > 0) return; 

        $sql->query(
            "
            insert into {$sql->table("mod:recentposts")}
            (board_id, bo_idx, mb_idx, mb_id, writer, subject, article, file1, file2, regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, now())
            ",
            array(
                $req['board_id'], $arr['idx'], $arr['mb_idx'], $arr['mb_id'], $arr['writer'], $arr['subject'], $arr['article'], $arr['file1'], $arr['file2']
            )
        );

    }

    //
    // modify
    //
    public function get_modify()
    {
        global $MB, $req;

        $sql = new Pdosql();

        // 게시글 정보를 가져옴
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".$req['board_id'])}
            where idx=:col1
            ",
            array(
                $req['read']
            )
        );
        $arr = $sql->fetchs();

        // writer 처리
        $req['writer'] = ($arr['mb_idx'] == $MB['idx'] && IS_MEMBER) ? $MB['name'] : $arr['writer'];

        // recentposts 기록
        $sql->query(
            "
            update {$sql->table("mod:recentposts")} set
            subject=:col3, article=:col4, writer=:col5, file1=:col6, file2=:col7
            where board_id=:col1 and bo_idx=:col2
            ",
            array(
                $req['board_id'], $req['read'], $req['subject'], $req['article'], $req['writer'], $arr['file1'], $arr['file2']
            )
        );
    }

    //
    // delete
    //
    public function get_delete()
    {
        global $MB, $req;

        $sql = new Pdosql();
        $boardlib = new Board_Library();
        $boardconf = $boardlib->load_conf($req['board_id']);

        // 게시글 정보를 가져옴
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".$req['board_id'])}
            where idx=:col1
            ",
            array(
                $req['read']
            )
        );
        $arr = $sql->fetchs();

        // 권한 check
        if ($MB['level'] <= $boardconf['ctr_level']) {
            $del_level = 1;

        } else if ($arr['mb_idx'] == $MB['idx'] && $MB['level'] <= $boardconf['delete_level']) {
            $del_level = 1;
        }

        // 패스워드가 submit된 경우 패스워드가 일치 하는지 검사
        if (isset($req['s_password']) && $arr['pwd'] == $req['s_password']) $del_level = 1;

        if ($del_level != 1) return;

        // recentposts 기록 삭제
        $sql->query(
            "
            delete
            from {$sql->table("mod:recentposts")}
            where board_id=:col1 and bo_idx=:col2
            ",
            array(
                $req['board_id'], $req['read']
            )
        );
    }

}

//
// Module Controller
// ( Set_recent_ctrl_submit )
//
class Set_recent_ctrl_submit {

    public function init()
    {
        global $req, $moduleconf;

        $sql = new Pdosql();

        $req = Method::request('post', 'type, board_id, t_board_id, cnum');

        $modulelib = new Recentposts_Library();
        $moduleconf = $modulelib->load_conf();
        $moduleconf = explode('|', $moduleconf['boards']);

        // route
        switch ($req['type']) {

            case 'del' :
                $this->get_del();
                break;

            case 'copy' :
                $this->get_copy();
                break;

            case 'move' :
                $this->get_move();
                break;
        }

    }

    //
    // delete
    //
    public function get_del()
    {
        global $req, $moduleconf;

        $sql = new Pdosql();

        // 권한 있는 게시판만 적용
        if (!in_array($req['board_id'], $moduleconf)) return;

        // 대상 게시글
        $data = explode(',', $req['cnum']);

        // recent posts 삭제
        foreach ($data as $key => $value) {
            $sql->query(
                "
                delete
                from {$sql->table("mod:recentposts")}
                where board_id=:col1 and bo_idx=:col2
                ",
                array(
                    $req['board_id'], $value
                )
            );
        }
    }

    //
    // copy
    //
    public function get_copy()
    {
        global $req ,$moduleconf;

        $sql = new Pdosql();

        // 대상 게시글
        $data = explode(',', $req['cnum']);
        $where_not_matchs = array();

        foreach ($data as $key => $value) {

            $sql->specialchars = 0;
            $sql->nl2br = 0;

            // recent posts 정보
            $sql->query(
                "
                select *
                from {$sql->table("mod:recentposts")}
                where board_id=:col1 and bo_idx=:col2
                ",
                array(
                    $req['board_id'], $value
                )
            );
            $recent_arr = $sql->fetchs();

            if ($sql->getcount() < 1) continue;

            // copy 된 게시글 정보
            $where = (!empty($where_not_matchs)) ? 'and ('.implode(' and ', $where_not_matchs).')' : '';

            $sql->query(
                "
                select idx
                from {$sql->table("mod:board_data_".$req['t_board_id'])}
                where subject=:col1 and article=:col2 {$where}
                order by regdate desc
                limit 1
                ",
                array(
                    $recent_arr['subject'], $recent_arr['article']
                )
            );
            $board_arr = $sql->fetchs();

            $where_not_matchs[] = "idx != '{$board_arr['idx']}'";

            if (!$board_arr['idx'] || !$board_arr['idx']) continue;

            // 권한 있는 게시판만 적용
            if (!in_array($req['t_board_id'], $moduleconf)) return;

            // recent posts 기록
            $sql->query(
                "
                insert into {$sql->table("mod:recentposts")}
                (board_id, bo_idx, mb_idx, mb_id, writer, subject, article, file1, file2, regdate)
                values
                (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, now())
                ",
                array(
                    $req['t_board_id'], $board_arr['idx'], $recent_arr['mb_idx'], $recent_arr['mb_id'], $recent_arr['writer'], $recent_arr['subject'], $recent_arr['article'], $recent_arr['file1'], $recent_arr['file2']
                )
            );

        }
    }

    //
    // move
    //
    public function get_move()
    {
        global $req, $moduleconf;

        $sql = new Pdosql();

        // 대상 게시글
        $data = explode(',', $req['cnum']);
        $where_not_matchs = array();

        foreach ($data as $key => $value) {

            $sql->specialchars = 0;
            $sql->nl2br = 0;

            // recent posts 정보
            $sql->query(
                "
                select *
                from {$sql->table("mod:recentposts")}
                where board_id=:col1 and bo_idx=:col2
                ",
                array(
                    $req['board_id'], $value
                )
            );
            $recent_arr = $sql->fetchs();

            // move 된 게시글 정보
            $where = (!empty($where_not_matchs)) ? 'and ('.implode(' and ', $where_not_matchs).')' : '';

            $sql->query(
                "
                select idx
                from {$sql->table("mod:board_data_".$req['t_board_id'])}
                where subject=:col1 and article=:col2 {$where}
                order by regdate desc
                limit 1
                ",
                array(
                    $recent_arr['subject'], $recent_arr['article']
                )
            );
            $board_arr = $sql->fetchs();

            $where_not_matchs[] = "idx != '{$board_arr['idx']}'";

            if (!$board_arr['idx']) continue;

            // recent posts 기록
            if (in_array($req['board_id'], $moduleconf)) {
                $sql->query(
                    "
                    delete
                    from {$sql->table("mod:recentposts")}
                    where board_id=:col1 and bo_idx=:col2
                    ",
                    array(
                        $req['board_id'], $value
                    )
                );
            }

            if (in_array($req['t_board_id'], $moduleconf)) {

                $sql->query(
                    "
                    insert into {$sql->table("mod:recentposts")}
                    (board_id, bo_idx, mb_idx, mb_id, writer, subject, article, file1, file2, regdate)
                    values
                    (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, now())
                    ",
                    array(
                        $req['t_board_id'], $board_arr['idx'], $recent_arr['mb_idx'], $recent_arr['mb_id'], $recent_arr['writer'], $recent_arr['subject'], $recent_arr['article'], $recent_arr['file1'], $recent_arr['file2']
                    )
                );

            }
        }
    }

}
