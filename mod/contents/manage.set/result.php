<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Make\Library\Mail;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/mod/contents/result/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_CONTENTS_PATH.'/manage.set/html/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function contents_total($arr)
        {
            return Func::number($arr['contents_total']);
        }
    }

    public function make()
    {
        global $PARAM, $sortby, $searchby, $orderby;

        $sql = new Pdosql();
        $paging = new Paging();
        $manage = new ManageFunc();

        // sortby
        $sortby = '';
        $sort_arr = array();

        $sql->query(
            "
            select
            (
                select count(*)
                from {$sql->table("mod:contents")}
            ) contents_total
            ", []
        );
        $sort_arr['contents_total'] = $sql->fetch('contents_total');

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mod:contents")}
                where 1 $sortby $searchby
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
                $arr['regdate'] = Func::datetime($arr['regdate']);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $pagingprint = $paging->pagingprint($manage->pag_def_param());

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('contents_total', contents_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);
    }

}

//
// Controller for display
// https://{domain}/manage/mod/contactform/result/regist
//
class Regist extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_CONTENTS_PATH.'/manage.set/html/regist.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {
        $manage = new ManageFunc();

        $this->set('manage', $manage);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makeContentsForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_CONTENTS_DIR.'/result/regist-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Regist )
//
class Regist_submit {

    public function init()
    {
        global $board_id;

        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'data_key, title, html, mo_html, use_mo_html');
        $manage->req_hidden_inp('post');

        Valid::get(
            array(
                'input' => 'data_key',
                'value' => $req['data_key'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("mod:contents")}
            where data_key=:col1
            order by regdate desc
            ",
            array(
                $req['data_key']
            )
        );

        if ($sql->getcount() > 0) Valid::error('key', '이미 존재하는 콘텐츠 key 입니다.');

        $req['use_mo_html'] = ($req['use_mo_html'] == 'checked') ? 'Y' : 'N';

        $sql->query(
            "
            insert into {$sql->table("mod:contents")}
            (data_key,title,html,mo_html,use_mo_html,regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                $req['data_key'], $req['title'], $req['html'], $req['mo_html'], $req['use_mo_html']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("mod:contents")}
            where data_key=:col1
            order by regdate desc
            ",
            array(
                $req['data_key']
            )
        );
        $idx = $sql->fetch('idx');

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 추가 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mod/'.MOD_CONTENTS.'/result/modify?idx='.$idx
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/contactform/result/modify
//
class Modify extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_CONTENTS_PATH.'/manage.set/html/modify.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function set_chked($arr, $val)
        {
            $setarr = array('Y' => '', 'N' => '');
            foreach($setarr as $key => $value){
                if ($key==$arr[$val]) $setarr[$key] = 'checked';
            }
            return $setarr;
        }
    }

    public function make()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        $req = Method::request('get', 'idx');

        $sql->query(
            "
            select *
            from {$sql->table("mod:contents")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('콘텐츠가 존재하지 않습니다.');

        $arr = $sql->fetchs();

        $sql->specialchars = 0;
        $sql->nl2br = 0;
        $arr['html'] = $sql->fetch('html');
        $arr['mo_html'] = $sql->fetch('mo_html');

        $write = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $write[$key] = $value;
            }

        } else {
            $write = null;
        }

        $this->set('manage', $manage);
        $this->set('write', $write);
        $this->set('use_mo_html', set_chked($arr, 'use_mo_html'));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'modifyContentsForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_CONTENTS_DIR.'/result/modify-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Modify )
//
class Modify_submit{

    public function init()
    {
        global $req;

        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, idx, title, html, mo_html, use_mo_html');
        $manage->req_hidden_inp('post');

        switch ($req['mode']) {
            case 'mod' :
                $this->get_modify();
                break;

            case 'del' :
                $this->get_delete();
                break;
        }
    }

    //
    // modify
    //
    public function get_modify()
    {
        global $req;

        $sql = new Pdosql();

        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );

        $req['use_mo_html'] = ($req['use_mo_html'] == 'checked') ? 'Y' : 'N';

        $sql->query(
            "
            update {$sql->table("mod:contents")}
            SET title=:col2, html=:col3, mo_html=:col4, use_mo_html=:col5
            where idx=:col1
            ",
            array(
                $req['idx'], $req['title'], $req['html'], $req['mo_html'], $req['use_mo_html']
            )
        );

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 변경 되었습니다.'
            )
        );
        Valid::turn();
    }

    //
    // delete
    //
    public function get_delete()
    {
        global $req;

        $sql = new Pdosql();
        $manage = new ManageFunc();

        $sql->query(
            "
            select *
            from {$sql->table("mod:contents")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Valid::error('', '콘텐츠가 존재하지 않습니다.');

        $sql->query(
            "
            delete
            from {$sql->table("mod:contents")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 삭제 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mod/'.MOD_CONTENTS.'/result/result'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}
