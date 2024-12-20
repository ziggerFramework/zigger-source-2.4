<?php
namespace Module\Board;

use Corelib\Method;
use Corelib\Valid;
use Corelib\Func;
use Make\Database\Pdosql;
use Make\Library\Uploader;
use Module\Board\Library as Board_Library;

//
// Module Controller
// ( Ctrl )
//
class Ctrl extends \Controller\Make_Controller {

    public function init()
    {
        global $boardconf, $req;

        $req = Method::request('post', 'cnum, board_id, page, category, where, keyword, sort, ordtg, ordsc, request');

        $boardlib = new Board_Library();
        $boardconf = $boardlib->load_conf($req['board_id']);

        $tpl = ($req['request'] == 'manage') ? MOD_BOARD_PATH.'/manage.set/html/ctrpop.tpl.php' : MOD_BOARD_THEME_PATH.'/board/'.$boardconf['theme'].'/ctrpop.tpl.php';
        $this->layout()->view($tpl);
    }

    public function func()
    {
        // 게시판 목록
        function board_opt_list()
        {
            global $req;

            $sql = new Pdosql();

            $sql->query(
                "
                select config.*,board_name_tbl.cfg_value as board_name
                from {$sql->table("config")} config
                left outer join {$sql->table("config")} board_name_tbl
                on config.cfg_type=board_name_tbl.cfg_type and board_name_tbl.cfg_key='title'
                where config.cfg_type like 'mod:board:config:%' and config.cfg_key='id'
                ", []
            );

            $opt = '';

            do {
                $arr = $sql->fetchs();

                $opt_slted = '';
                if ($req['board_id'] == $arr['cfg_value']) $opt_slted = 'selected';
                $opt .= '<option value="'.$arr['cfg_value'].'" '.$opt_slted.'>'.$arr['board_name'].'('.$arr['cfg_value'].')</option>';

            } while ($sql->nextRec());

            return $opt;
        }
    }

    public function make()
    {
        global $boardconf, $req;

        $arr = array();
        for ($i = 0; $i < count($req['cnum']); $i++) {
            $arr[] = $req['cnum'][$i];
        }

        $cnum_arr = implode(',', $arr);

        $this->set('req', $req);
        $this->set('slt_count', sizeof($req['cnum']));
        $this->set('board_opt_list', board_opt_list());
        $this->set('cnum_arr', $cnum_arr);
        $this->set('board_id', $req['board_id']);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board_ctrpopForm');
        $form->set('type', 'html');
        $form->set('action', MOD_BOARD_DIR.'/controller/pop/ctrl-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Ctrl )
//
class Ctrl_submit {

    public function init()
    {
        global $MB, $boardconf, $req, $cnum, $board_id, $t_board_id;

        $boardlib = new Board_Library();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'cnum, type, board_id, t_board_id, category, page, where, keyword, thisuri, request, sort, ordtg, ordsc, request');

        $board_id = $req['board_id'];
        $t_board_id = $req['t_board_id'];

        // board_id, t_board_id 값 검증
        if (!preg_match(REGEXP_IDX, $board_id) || !preg_match(REGEXP_IDX, $t_board_id)) Valid::error('', '원본 게시판 또는 대상 게시판 id 값이 올바르지 않습니다.');

        //load config
        $boardconf = $boardlib->load_conf($board_id);

        //관리 권한 검사
        if ($MB['level'] > $boardconf['ctr_level']) {
            Valid::error('', '글을 관리할 권한이 없습니다.');
        }

        //게시물 번호 분리
        $cnum = explode(',', $req['cnum']);
        $cnum = array_reverse($cnum);

        switch ($req['type']) {

            case 'del' :
                $this->get_del();
                break;

            case 'move' :
                $this->get_move();
                break;

            case 'copy' :
                $this->get_copy();
                break;

        }
    }

    //
    // 게시물 삭제
    //
    private function get_del()
    {
        global $CONF, $board_id, $del_where_sum, $cnum, $req;

        $uploader = new Uploader();
        $sql = new Pdosql();
        $sql2 = new Pdosql();

        $del_where = array();

        for ($i = 0; $i < count($cnum); $i++) {

            if ($cnum[$i] != '') {

                // 원글 게시물 정보
                $sql->query(
                    "
                    select *
                    from {$sql->table("mod:board_data_".$board_id)}
                    where `idx`=:col1
                    ",
                    array(
                        $cnum[$i]
                    )
                );
                $org_arr = $sql->fetchs();

                // 최소/최대 ln값 구함
                $ln_min = (int)(ceil($org_arr['ln'] / 1000) * 1000) - 1000;
                $ln_max = (int)(ceil($org_arr['ln'] / 1000) * 1000);

                // 부모글인 경우 범위 조건문 구함
                if ($org_arr['rn'] == 0) $del_where[$i] = '(ln>'.$ln_min.' and ln<='.$ln_max.')';

                // 자식글(답글)인 경우 범위 조건문 구함
                if ($org_arr['rn'] >= 1) {
                    $sql->query(
                        "
                        select `ln`
                        from {$sql->table("mod:board_data_".$board_id)}
                        where `ln`>=:col1 and `ln`<:col2 and `rn`=:col3
                        order by `ln` desc
                        limit 1
                        ",
                        array(
                            $ln_min, $org_arr['ln'], $org_arr['rn']
                        )
                    );
                    $tar_ln = $sql->fetch('ln');

                    $del_where[$i] = ($tar_ln == '') ? '(`ln`<='.$org_arr['ln'].' and `ln`>'.$ln_min.' and `rn`>='.$org_arr['rn'].')' : '(`ln`<='.$org_arr['ln'].' and `ln`>'.$tar_ln.' and `rn`>='.$org_arr['rn'].')';
                }
            }
        }

        // 삭제 범위 조건문을 하나의 구문으로 합침
        for ($i = 0; $i < count($del_where); $i++) {
            if ($i == 0) {
                $del_where_sum = $del_where[$i];

            } else {
                $del_where_sum .= ' or '.$del_where[$i];
            }
        }

        // 삭제 범위 내 게시물 정보
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".$board_id)}
            where $del_where_sum
            ", []
        );

        // 첨부파일 삭제
        if ($sql->getcount() > 0) {
            do {
                $del_arr = $sql->fetchs();

                $sql2->query(
                    "
                    select *
                    from {$sql2->table("mod:board_files")}
                    where `id`=:col1 and `data_idx`=:col2
                    ",
                    array(
                        $board_id, $del_arr['idx']
                    )
                );

                if ($sql2->getcount() < 1) continue;
                
                do {
                    $file_arr = $sql2->fetchs();

                    if ($file_arr['file_name']) {
                        // file lookup
                        $fileinfo = Func::get_fileinfo($file_arr['file_name']);

                        // 원본 파일 삭제
                        $uploader->path = PH_DATA_PATH.$fileinfo['filepath'];
                        $uploader->drop($file_arr['file_name']);

                        // 썸네일이 있다면 삭제
                        if ($CONF['use_s3'] == 'N' && $uploader->isfile(PH_DATA_PATH.$fileinfo['filepath'].'/thumb/'.$file_arr['file_name'])) {
                            $uploader->path = PH_DATA_PATH.$fileinfo['filepath'].'/thumb';
                            $uploader->drop($file_arr['file_name'], false);
                        }
                    }

                } while ($sql2->nextRec());

                $sql2->query(
                    "
                    delete
                    from {$sql2->table("mod:board_files")}
                    where `id`=:col1 and `data_idx`=:col2
                    ",
                    array(
                        $board_id, $del_arr['idx']
                    )
                );
                
            } while ($sql->nextRec());
        }

        // 댓글 삭제
        if ($sql->getcount() > 0) {
            do {
                $del_arr['idx'] = $sql->fetch('idx');
                $sql2->query(
                    "
                    delete
                    from {$sql2->table("mod:board_cmt_".$board_id)}
                    where `bo_idx`=:col1
                    ",
                    array(
                        $del_arr['idx']
                    )
                );
            } while ($sql->nextRec());
        }

        // 게시글 삭제
        $sql->query(
            "
            delete
            from {$sql->table("mod:board_data_".$board_id)}
            where $del_where_sum
            ", []
        );

        // return
        $return_url = '?page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category']);

        if (isset($req['request']) && $req['request'] == 'manage') {
            $return_url = '?page='.$req['page'].'&sort='.$req['sort'].'&ordtg='.$req['ordtg'].'&ordsc='.$req['ordsc'].'&category='.urlencode($req['category']).'&id='.$board_id.'&where='.$req['where'].'&keyword='.$req['keyword'];
        }

        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => $return_url,
                'msg' => '성공적으로 삭제 되었습니다.'
            )
        );
        Valid::turn();
    }

    //
    // 게시물 이동
    //
    private function get_move()
    {
        global $CONF, $board_id, $t_board_id, $ln_where, $cnum, $req;

        $uploader = new Uploader();

        $sql = new Pdosql();
        $cp_sql = new Pdosql();
        $cp_sql2 = new Pdosql();

        // 선택된 게시물의 ln,rn 정보
        $ln_where = array();
        for ($i = 0; $i < count($cnum); $i++) {
            if ($i == 0) {
                $ln_where = '`idx`=\''.$cnum[$i].'\'';

            } else {
                $ln_where .= ' or `idx`=\''.$cnum[$i].'\'';
            }
        }

        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".$board_id)}
            where $ln_where
            ", []
        );

        $i = 0;

        do {
            $ln_arr = $sql->fetchs();
            $ln[$i] = $ln_arr['ln'];
            $rn[$i] = $ln_arr['rn'];
            $i++;
        } while ($sql->nextRec());

        // 이동 실행
        for ($i = 0; $i < count($cnum); $i++) {

            // 부모글인 경우에만 이동 실행
            if ($rn[$i] == 0) {

                // 글의 최소/최대 ln값 구함
                $ln_min = (int)(ceil($ln[$i] / 1000) * 1000) - 1000;
                $ln_max = (int)(ceil($ln[$i] / 1000) * 1000);

                // 대상 게시판이 존재하는지 검증
                if ($sql->table_exists(DB_PREFIX.'_mod_board_data_'.$t_board_id)) Valid::error('', '대상 게시판 id 값이 올바르지 않습니다.');

                // table 정의
                $board_data_table = str_replace(['`', '\`'], '', $sql->table("mod:board_data_".addslashes($board_id)));
                $t_board_data_table = str_replace(['`', '\`'], '', $sql->table("mod:board_data_".addslashes($t_board_id)));
                $board_cmt_table = str_replace(['`', '\`'], '', $sql->table("mod:board_cmt_".addslashes($board_id)));
                $t_board_cmt_table = str_replace(['`', '\`'], '', $sql->table("mod:board_cmt_".addslashes($t_board_id)));

                // 자식글의 범위를 구함
                $ln_where = 'ln>'.$ln_min.' and ln<='.$ln_max;
                $sql->query(
                    "
                    select *
                    from {$board_data_table}
                    where $ln_where
                    ", []
                );

                // 대상 게시판의 최대 ln값 불러옴
                $cp_sql->query(
                    "
                    select max(`ln`)+1000 as ln_max
                    from {$t_board_data_table}
                    order by `ln` desc
                    limit 1
                    ", []
                );

                $tar_ln = $cp_sql->fetch('ln_max');

                if (!$tar_ln) $tar_ln = 1000;
                $tar_ln = ceil($tar_ln / 1000) * 1000;

                // 복사 대상 범위에 해당하는 게시물의 이동 시작
                do {
                    $sql->specialchars = 0;
                    $sql->nl2br = 0;
                    $arr = $sql->fetchs();

                    // 원본 글의 첨부파일 정보 가져옴
                    $cp_sql->query(
                        "
                        select *
                        from {$cp_sql->table("mod:board_files")}
                        where `id`=:col1 and `data_idx`=:col2
                        ",
                        array(
                            $board_id, $arr['idx']
                        )
                    );

                    $uploaded_files = array();
                    $upload_files = array();

                    if ($cp_sql->getcount() > 0) {
                        do {
                            $files_arr = $cp_sql->fetchs();
                            $uploaded_files[$files_arr['file_seq']] = $files_arr;

                        } while ($cp_sql->nextRec());
                    }

                    // 대상 게시판으로 첨부파일 복사
                    if (!empty($uploaded_files)) {
                        foreach ($uploaded_files as $key => $value) {
                            if (!$value) continue;

                            // file lookup
                            $fileinfo = Func::get_fileinfo($value['file_name']);

                            // path
                            $upload_dir = date('ym');

                            $old_path = PH_DATA_PATH.$fileinfo['filepath'];
                            $uploader->path = MOD_BOARD_DATA_PATH.'/'.$t_board_id.'/'.$upload_dir.'/thumb';
                            $uploader->chkpath();
                            $tar_path = $uploader->path = MOD_BOARD_DATA_PATH.'/'.$t_board_id.'/'.$upload_dir;
                            $uploader->chkpath();

                            // copy
                            $upload_files[$key] = $value;
                            $upload_files[$key]['file_name'] = $uploader->replace_filename($value['file_name']);
                            
                            $uploader->filecopy($old_path.'/'.$value['file_name'], $tar_path.'/'.$upload_files[$key]['file_name']);

                            if ($uploader->isfile($old_path.'/thumb/'.$value['file_name'])) {
                                $uploader->filecopy($old_path.'/thumb/'.$value['file_name'], $tar_path.'/thumb/'.$upload_files[$key]['file_name'], false);
                            }

                            // delete old file
                            $uploader->path = $old_path;
                            $uploader->drop($value['file_name']);
                            $uploader->path = $old_path.'/thumb';
                            $uploader->drop($value['file_name'], false);
                        }
                    }

                    // 첨부파일 종류 기록
                    $ufile_icon_type = '';

                    if (!empty($upload_files)) {
                        $ufile_icon_type = 'file';

                        foreach ($upload_files as $key => $value) {
                            $file_type = Func::get_filetype($value['file_name']);
                            if (Func::chkintd('match', $file_type, SET_IMGTYPE)) $ufile_icon_type = 'image';
                        }
                    }
                    
                    // 게시글 대표이미지 생성
                    $ufile_tmb_filename = '';

                    preg_match(REGEXP_IMG, Func::htmldecode($arr['article']), $match);

                    if (!empty($upload_files)) {
                        foreach ($upload_files as $key => $value) {
                            $file_type = Func::get_filetype($value['file_name']);

                            if (Func::chkintd('match', $file_type, SET_IMGTYPE)) {
                                $ufile_tmb_filename = $value['file_name'];
                            }
                        }
                    }

                    if (!$ufile_tmb_filename) {
                        $ufile_tmb_filename = (isset($match[1])) ? basename($match[1]) : '';
                    }

                    // 대상 게시판으로 글을 복사
                    $cp_dregdate = null;
                    if ($arr['dregdate']) $cp_dregdate = $arr['dregdate'];

                    $cp_sql->query(
                        "
                        insert into
                        {$t_board_data_table}
                        (`category`, `ln`, `rn`, `mb_idx`, `mb_id`, `writer`, `pwd`, `email`, `article`, `subject`, `file1`, `file1_cnt`, `file2`, `file2_cnt`, `use_secret`, `use_html`, `use_email`, `view`, `ip`, `regdate`, `dregdate`, `data_1`, `data_2`, `data_3`, `data_4`, `data_5`, `data_6`, `data_7`, `data_8`, `data_9`, `data_10`)
                        values
                        (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, :col11, :col12, :col13, :col14, :col15, :col16, :col17, :col18, :col19, now(), :col20, :col21, :col22, :col23, :col24, :col25, :col26, :col27, :col28, :col29, :col30)
                        ",
                        array(
                            $arr['category'], $tar_ln,$arr['rn'], $arr['mb_idx'], $arr['mb_id'], $arr['writer'], $arr['pwd'], $arr['email'], $arr['article'], $arr['subject'],
                            $ufile_icon_type, $arr['file1_cnt'], $ufile_tmb_filename, $arr['file2_cnt'], $arr['use_secret'], $arr['use_html'], $arr['use_email'], $arr['view'], $arr['ip'], $cp_dregdate,
                            $arr['data_1'], $arr['data_2'], $arr['data_3'], $arr['data_4'], $arr['data_5'], $arr['data_6'], $arr['data_7'], $arr['data_8'], $arr['data_9'], $arr['data_10']
                        )
                    );

                    // 이동된 글의 idx값을 다시 불러옴
                    $cp_sql->query(
                        "
                        select `idx`
                        from {$t_board_data_table}
                        where `ln`=:col1
                        ",
                        array(
                            $tar_ln
                        )
                    );
                    $cped_idx = $cp_sql->fetch('idx');

                    // 첨부파일 정보 이동
                    if (!empty($uploaded_files)) {

                        // 파일 복사
                        $insert_qry = array();

                        foreach ($upload_files as $key => $value) {
                            $insert_qry[] = "(:col1, :col2, '".addslashes($key)."', '".addslashes($value['file_name'])."', '".addslashes($value['file_cnt'])."', now())";
                        }

                        $insert_qry = implode(',', $insert_qry);

                        $cp_sql->query(
                            "
                            insert into
                            {$cp_sql->table("mod:board_files")}
                            (`id`, `data_idx`, `file_seq`, `file_name`, `file_cnt`, `regdate`)
                            values
                            ".$insert_qry,
                            array(
                                $t_board_id, $cped_idx, 
                            )
                        );

                        // 기존 파일 삭제
                        $cp_sql->query(
                            "
                            delete
                            from {$cp_sql->table("mod:board_files")}
                            where `id`=:col1 and `data_idx`=:col2
                            ",
                            array(
                                $board_id, $arr['idx'], 
                            )
                        );
                    }

                    // 좋아요 이동
                    $cp_sql->query(
                        "
                        update
                        {$cp_sql->table("mod:board_like")}
                        set `id`=:col1, `data_idx`=:col2
                        where `id`=:col3 and `data_idx`=:col4
                        ",
                        array(
                            $t_board_id, $cped_idx, $board_id, $arr['idx']
                        )
                    );

                    // 댓글 복사를 위해 대상 댓글 테이블의 최대 ln값 구함
                    $cp_sql->query(
                        "
                        select max(`ln`)+1000 as ln_max
                        from {$t_board_data_table}
                        order by `ln` desc
                        limit 1
                        ", []
                    );

                    $c_tar_ln = $cp_sql->fetch('ln_max');
                    if (!$c_tar_ln) $c_tar_ln = 1000;
                    $c_tar_ln = ceil($c_tar_ln / 1000) * 1000;

                    // 댓글 복사를 위해 원본 댓글 테이블의 댓글 정보 가져옴
                    $cp_sql->query(
                        "
                        select *
                        from {$board_cmt_table}
                        where `bo_idx`=:col1
                        ",
                        array(
                            $arr['idx']
                        )
                    );

                    if ($cp_sql->getcount() > 0) {
                        do {
                            $cp_sql->specialchars = 0;
                            $cp_sql->nl2br = 0;
                            $cmt_arr = $cp_sql->fetchs();

                            $cp_sql2->query(
                                "
                                insert into
                                {$t_board_cmt_table}
                                (`ln`, `rn`, `bo_idx`, `mb_idx`, `writer`, `parent_writer`, `parent_mb_idx`, `comment`, `ip`, `regdate`, `cmt_1`, `cmt_2`, `cmt_3`, `cmt_4`, `cmt_5`, `cmt_6`, `cmt_7`, `cmt_8`, `cmt_9`, `cmt_10`)
                                values
                                (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, :col11, :col12, :col13, :col14, :col15, :col16, :col17, :col18, :col19, :col20)
                                ",
                                array(
                                    $cmt_arr['ln'], $cmt_arr['rn'], $cped_idx, $cmt_arr['mb_idx'], $cmt_arr['writer'], $cmt_arr['parent_writer'], $cmt_arr['parent_mb_idx'], $cmt_arr['comment'], $cmt_arr['ip'], $cmt_arr['regdate'],
                                    $cmt_arr['cmt_1'], $cmt_arr['cmt_2'], $cmt_arr['cmt_3'], $cmt_arr['cmt_4'], $cmt_arr['cmt_5'], $cmt_arr['cmt_6'], $cmt_arr['cmt_7'], $cmt_arr['cmt_8'], $cmt_arr['cmt_9'], $cmt_arr['cmt_10']
                                )
                            );

                        } while ($cp_sql->nextRec());
                    }

                    // 기존 댓글 삭제
                    $cp_sql->query(
                        "
                        delete
                        from {$board_cmt_table}
                        where `bo_idx`=:col1
                        ",
                        array(
                            $arr['idx']
                        )
                    );

                    // 원본글 삭제
                    $cp_sql->query(
                        "
                        delete
                        from {$board_data_table}
                        where `idx`=:col1
                        ",
                        array(
                            $arr['idx']
                        )
                    );

                    $tar_ln--;

                } while ($sql->nextRec());

            }
        }

        // return
        $return_url = '?page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category']);

        if (isset($req['request']) && $req['request'] == 'manage') {
            $return_url = '?page='.$req['page'].'&sort='.$req['sort'].'&ordtg='.$req['ordtg'].'&ordsc='.$req['ordsc'].'&category='.urlencode($req['category']).'&id='.$board_id.'&where='.$req['where'].'&keyword='.$req['keyword'];
        }

        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => $return_url,
                'msg' => '성공적으로 이동 되었습니다.'
            )
        );
        Valid::turn();
    }

    //
    // 게시물 복사
    //
    private function get_copy()
    {
        global $CONF, $board_id, $t_board_id, $cnum, $req;

        $uploader = new Uploader();
        $sql = new Pdosql();

        for ($i = 0; $i < count($cnum); $i++) {

            // 대상 게시판이 존재하는지 검증
            if ($sql->table_exists(DB_PREFIX.'_mod_board_data_'.$t_board_id)) Valid::error('', '대상 게시판 id 값이 올바르지 않습니다.');

            // table 정의
            $board_data_table = str_replace(['`', '\`'], '', $sql->table("mod:board_data_".addslashes($board_id)));
            $t_board_data_table = str_replace(['`', '\`'], '', $sql->table("mod:board_data_".addslashes($t_board_id)));
            $board_cmt_table = str_replace(['`', '\`'], '', $sql->table("mod:board_cmt_".addslashes($board_id)));
            $t_board_cmt_table = str_replace(['`', '\`'], '', $sql->table("mod:board_cmt_".addslashes($t_board_id)));

            // 원본글의 정보를 불러옴
            $sql->query(
                "
                select *
                from {$board_data_table}
                where `idx`=:col1
                ",
                array(
                    $cnum[$i]
                )
            );
            $sql->specialchars = 0;
            $sql->nl2br = 0;
            $arr = $sql->fetchs();

            // 부모글인 경우만 복사 실행
            if($arr['rn'] == 0){

                // 원본 글의 첨부파일 정보 가져옴
                $sql->query(
                    "
                    select *
                    from {$sql->table("mod:board_files")}
                    where `id`=:col1 and `data_idx`=:col2
                    ",
                    array(
                        $board_id, $arr['idx']
                    )
                );

                $uploaded_files = array();
                $upload_files = array();

                if ($sql->getcount() > 0) {
                    do {
                        $files_arr = $sql->fetchs();
                        $uploaded_files[$files_arr['file_seq']] = $files_arr;

                    } while ($sql->nextRec());
                }

                // 대상 게시판의 최대 ln값 불러옴
                $sql->query(
                    "
                    select max(`ln`)+1000 as ln_max
                    from {$t_board_data_table}
                    order by `ln` desc
                    limit 1
                    ", []
                );

                $tar_ln = $sql->fetch('ln_max');
                if (!$tar_ln) $tar_ln = 1000;
                $tar_ln = ceil($tar_ln / 1000) * 1000;

                // 대상 게시판으로 첨부파일 복사
                if (!empty($uploaded_files)) {
                    foreach ($uploaded_files as $key => $value) {
                        if (!$value) continue;

                        // file lookup
                        $fileinfo = Func::get_fileinfo($value['file_name']);

                        // path
                        $upload_dir = date('ym');

                        $old_path = PH_DATA_PATH.$fileinfo['filepath'];
                        $uploader->path = MOD_BOARD_DATA_PATH.'/'.$t_board_id.'/'.$upload_dir.'/thumb';
                        $uploader->chkpath();
                        $tar_path = $uploader->path = MOD_BOARD_DATA_PATH.'/'.$t_board_id.'/'.$upload_dir;
                        $uploader->chkpath();

                        // copy
                        $upload_files[$key] = $value;
                        $upload_files[$key]['file_name'] = $uploader->replace_filename($value['file_name']);
                        
                        $uploader->filecopy($old_path.'/'.$value['file_name'], $tar_path.'/'.$upload_files[$key]['file_name']);

                        if ($uploader->isfile($old_path.'/thumb/'.$value['file_name'])) {
                            $uploader->filecopy($old_path.'/thumb/'.$value['file_name'], $tar_path.'/thumb/'.$upload_files[$key]['file_name'], false);
                        }
                    }
                }

                // 첨부파일 종류 기록
                $ufile_icon_type = '';

                if (!empty($upload_files)) {
                    $ufile_icon_type = 'file';

                    foreach ($upload_files as $key => $value) {
                        $file_type = Func::get_filetype($value['file_name']);
                        if (Func::chkintd('match', $file_type, SET_IMGTYPE)) $ufile_icon_type = 'image';
                    }
                }
                
                // 게시글 대표이미지 생성
                $ufile_tmb_filename = '';

                preg_match(REGEXP_IMG, Func::htmldecode($arr['article']), $match);

                if (!empty($upload_files)) {
                    foreach ($upload_files as $key => $value) {
                        $file_type = Func::get_filetype($value['file_name']);

                        if (Func::chkintd('match', $file_type, SET_IMGTYPE)) {
                            $ufile_tmb_filename = $value['file_name'];
                        }
                    }
                }

                if (!$ufile_tmb_filename) {
                    $ufile_tmb_filename = (isset($match[1])) ? basename($match[1]) : '';
                }

                // 대상 게시판으로 글을 복사
                $cp_dregdate = null;

                if ($arr['dregdate']) $cp_dregdate = $arr['dregdate'];

                $sql->query(
                    "
                    insert into
                    {$t_board_data_table}
                    (`category`, `ln`, `rn`, `mb_idx`, `mb_id`, `writer`, `pwd`, `email`, `article`, `subject`, `file1`, `file1_cnt`, `file2`, `file2_cnt`, `use_secret`, `use_html`, `use_email`, `view`, `ip`, `regdate`, `dregdate`, `data_1`, `data_2`, `data_3`, `data_4`, `data_5`, `data_6`, `data_7`, `data_8`, `data_9`, `data_10`)
                    values
                    (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, :col11, :col12, :col13, :col14, :col15, :col16, :col17, :col18, :col19, now(), :col20, :col21, :col22, :col23, :col24, :col25, :col26, :col27, :col28, :col29, :col30)
                    ",
                    array(
                        $arr['category'], $tar_ln, $arr['rn'], $arr['mb_idx'], $arr['mb_id'], $arr['writer'], $arr['pwd'], $arr['email'], $arr['article'], $arr['subject'],
                        $ufile_icon_type, 0, $ufile_tmb_filename, 0, $arr['use_secret'], $arr['use_html'], $arr['use_email'], 0, $arr['ip'], $cp_dregdate,
                        $arr['data_1'], $arr['data_2'], $arr['data_3'], $arr['data_4'], $arr['data_5'], $arr['data_6'], $arr['data_7'], $arr['data_8'], $arr['data_9'], $arr['data_10']
                    )
                );

                // 복사된 글의 idx값을 다시 불러옴
                $sql->query(
                    "
                    select `idx`
                    from {$t_board_data_table}
                    where `ln`=:col1
                    ",
                    array(
                        $tar_ln
                    )
                );
                $cped_idx = $sql->fetch('idx');

                // 첨부파일 정보 복사
                if (!empty($uploaded_files)) {

                    $insert_qry = array();

                    foreach ($upload_files as $key => $value) {
                        $insert_qry[] = "(:col1, :col2, '".addslashes($key)."', '".addslashes($value['file_name'])."', '".addslashes($value['file_cnt'])."', now())";
                    }

                    $insert_qry = implode(',', $insert_qry);

                    $sql->query(
                        "
                        insert into
                        {$sql->table("mod:board_files")}
                        (`id`, `data_idx`, `file_seq`, `file_name`, `file_cnt`, `regdate`)
                        values
                        ".$insert_qry,
                        array(
                            $t_board_id, $cped_idx, 
                        )
                    );
                }
            }
        }

        // return
        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 복사 되었습니다.'
            )
        );
        Valid::turn();
    }

}

//
// Module Controller
// ( Writer )
//
class Writer extends \Controller\Make_Controller {

    public function init()
    {
        global $boardconf, $req;

        $boardlib = new Board_Library();

        $req = Method::request('get', 'board_id, mb_idx, thisuri');

        //load config
        $boardconf = $boardlib->load_conf($req['board_id']);

        $this->layout()->view(MOD_BOARD_THEME_PATH.'/board/'.$boardconf['theme'].'/mbpop.tpl.php');
    }

    public function func()
    {
        // 성별
        function gender($mbinfo)
        {
            global $CONF;

            if ($CONF['use_mb_gender'] != 'Y') return '';
            return ($mbinfo['mb_gender'] == 'M') ? '(남자)' : '(여자)';
        }

        // 작성글 보기 링크
        function get_link($mbinfo, $uri = '')
        {
            return $uri.'?where=mb_id&keyword='.$mbinfo['mb_id'];
        }

        // 프로필 이미지
        function get_profileimg($mbinfo)
        {
            if ($mbinfo['mb_profileimg']) {
                $fileinfo = Func::get_fileinfo($mbinfo['mb_profileimg']);
                return (isset($fileinfo['replink']) && !empty($fileinfo['replink'])) ? $fileinfo['replink'] : false;

            } else {
                return false;
            }
        }
    }

    public function make()
    {
        global $boardconf, $req;

        $sql = new Pdosql();

        // 회원 정보
        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where `mb_idx`=:col1
            limit 1
            ",
            array(
                $req['mb_idx']
            )
        );
        $mbinfo = $sql->fetchs();

        if (!isset($req['mb_idx']) || $sql->getcount() < 1) Func::err_location(ERR_MSG_1, PH_DOMAIN);

        $mbinfo['mb_regdate'] = Func::datetime($mbinfo['mb_regdate']);
        $mbinfo['mb_lately'] = Func::datetime($mbinfo['mb_lately']);
        $mbinfo[0]['mb_profileimg'] = get_profileimg($mbinfo);

        $is_mbinfo_show = (IS_MEMBER && !$mbinfo['mb_dregdate']) ? true : false;

        $this->set('mbinfo', $mbinfo);
        $this->set('is_mbinfo_show', $is_mbinfo_show);
        $this->set('gender', gender($mbinfo));
        $this->set('get_link', get_link($mbinfo, $req['thisuri']));
    }

}

//
// Module Controller
// ( Temporary )
//
class Temporary extends \Controller\Make_Controller {

    public function init()
    {
        global $boardconf, $req;

        $boardlib = new Board_Library();

        $req = Method::request('get', 'request, board_id, temp_hash');

        //load config
        $boardconf = $boardlib->load_conf($req['board_id']);

        $tpl = (isset($req['request']) && $req['request'] == 'manage') ? MOD_BOARD_PATH.'/manage.set/html/temppop.tpl.php' : MOD_BOARD_THEME_PATH.'/board/'.$boardconf['theme'].'/temppop.tpl.php';
        $this->layout()->view($tpl);
        
    }

    public function make()
    {
        global $MB, $req;
        
        $sql = new Pdosql();

        // 자신의 임시저장글 가져옴
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_temporary")}
            where `mb_idx`=:col1
            order by `regdate` desc
            limit 30
            ",
            array(
                $MB['idx']
            )
        );

        $print_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $arr = $sql->fetchs();

                $arr['regdate'] = Func::datetime($arr['regdate']);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('print_arr', $print_arr);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board_temporaryForm');
        $form->set('type', 'html');
        $form->set('action', MOD_BOARD_DIR.'/controller/pop/temporary-submit');
        $form->run();
    }

}


//
// Controller for submit
// ( Temporary )
//
class Temporary_submit {

    public function init()
    {
        global $MB, $req;

        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'temp_hash');

        if (!$req['temp_hash']) Valid::error('', ERR_MSG_1);

        // 임시글 삭제
        $sql->query(
            "
            delete
            from {$sql->table("mod:board_temporary")}
            where mb_idx=:col1 and hash=:col2
            ",
            array(
                $MB['idx'], $req['temp_hash']
            )
        );
        
        // return
        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'get_board_after_tempdata_delete(\''.$req['temp_hash'].'\')',
            )
        );
        Valid::turn();
    }

}