<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/mod/recentposts/result/setting
//
class Setting extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_RECENTPOSTS_PATH.'/manage.set/html/setting.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {
        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:recentposts:config' and cfg_key='boards'
            ", []
        );

        $arr = array();

        do {
            $cfg = $sql->fetchs();
            $arr[$cfg['cfg_key']] = $cfg['cfg_value'];

        } while($sql->nextRec());

        $this->set('write', $arr);
    }

    public function form()
    {

        $form = new \Controller\Make_View_Form();
        $form->set('id', 'recentpostsSettingForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_RECENTPOSTS.'/result/setting-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Setting )
//
class Setting_submit {

    public function init()
    {
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'boards');

        // check
        if(Valid::match('/^(?:[a-zA-Z0-9_]+(?:\|[a-zA-Z0-9_]+)*)$/', $req['boards']) === false) Valid::error('', '게시판 id를 올바르게 지정하세요.');
        
        $boards_exp = explode('|', $req['boards']);

        foreach ($boards_exp as $key => $value) {
            if(!$sql->table_exists('mod:board_data_'.$value)) Valid::error('', '존재하지 않는 게시판('.$value.')이 지정 되었습니다.');
        }

        $sql->query(
            "
            update
            {$sql->table("config")}
            set
            cfg_value=:col1
            where cfg_type='mod:recentposts:config' and cfg_key='boards'
            ",
            array(
                $req['boards']
            )
        );

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 반영 되었습니다.'
            )
        );
        Valid::turn();
    }
}

//
// Controller for display
// https://{domain}/manage/mod/recentposts/result/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_RECENTPOSTS_PATH.'/manage.set/html/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function board_total($arr)
        {
            return Func::number($arr['board_total']);
        }

        function data_total($arr)
        {
            global $board_id;

            $sql = new Pdosql();

            $board_id = $arr['cfg_value'];

            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$board_id)}
                where use_notice='Y' or use_notice='N'
                ",
                array(
                    $board_id
                )
            );
            return Func::number($sql->getcount());
        }
    }

    public function make()
    {
        global $PARAM, $sortby, $orderby;

        $sql = new Pdosql();
        $paging = new Paging();
        $manage = new ManageFunc();

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mod:recentposts")}
                order by $orderby
                ", []
            )
        );
        $list_cnt = $sql->getcount();
        $total_cnt = Func::number($paging->totalCount);
        $print_arr = array();

        if ($list_cnt > 0) {
            do {
                $arr = $sql->fetchs();

                $arr['no'] = $paging->getnum();
                
                $arr[0]['board-link'] = PH_MANAGE_DIR.'/mod/'.MOD_BOARD.'/result/board?id='.$arr['board_id'];
                $arr[0]['view-link'] = PH_MANAGE_DIR.'/mod/'.MOD_BOARD.'/result/board-view?id='.$arr['board_id'].'&read='.$arr['bo_idx'];
                $arr[0]['member-link'] = PH_MANAGE_DIR.'/member/modify?idx='.$arr['mb_idx'];
                $arr[0]['regdate'] = Func::datetime($arr['regdate']);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);

    }

    public function form($idx)
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makeBoardForm'.$idx);
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_BOARD_DIR.'/result/result-clone-submit');
        $form->run();
    }

}