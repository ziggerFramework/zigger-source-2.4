<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/popup/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/popup/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function pop_total($arr)
        {
            return Func::number($arr['pop_total']);
        }

        function use_pop($arr)
        {
            return Func::number($arr['use_pop']);
        }

        function notuse_pop($arr)
        {
            return Func::number($arr['notuse_pop']);
        }

        function thumbnail($arr)
        {
            preg_match(REGEXP_IMG, Func::htmldecode($arr['html']), $match);

            if (isset($match[0])) {
                $src = str_replace(PH_DATA_DIR.PH_PLUGIN_CKEDITOR.'/', PH_DATA_DIR.PH_PLUGIN_CKEDITOR.'/thumb/', $match[1]);
                $tmb = $src;

            } else {
                $tmb = '';
            }

            return $tmb;
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
                from {$sql->table("popup")}
            ) pop_total,
            (
                select count(*)
                from {$sql->table("popup")}
                where show_from<now() and show_to>now()
            ) use_pop,
            (
                select count(*)
                from {$sql->table("popup")}
                where (show_from>now() or show_to<now())
            ) notuse_pop
            ", []
        );
        $sort_arr['pop_total'] = $sql->fetch('pop_total');
        $sort_arr['use_pop'] = $sql->fetch('use_pop');
        $sort_arr['notuse_pop'] = $sql->fetch('notuse_pop');

        switch ($PARAM['sort']) {
            case 'usepop' :
                $sortby = 'and show_from<now() and show_to>now()';
                break;

            case 'nousepop' :
                $sortby = 'and (show_from>now() or show_to<now())';
                break;
        }

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("popup")}
                where 1 $sortby $searchby
                ORDER BY $orderby
                ", []
            )
        );
        $list_cnt = $sql->getcount();
        $total_cnt = Func::number($paging->totalCount);
        $print_arr = array();

        if ($list_cnt > 0) {
            do {
                $arr = $sql->fetchs();

                $arr['show'] = Func::date($arr['show_from']).' ~ '.Func::date($arr['show_to']);
                $arr['level'] = $arr['level_from'].' ~ '.$arr['level_to'];
                $arr['no'] = $paging->getnum();
                $arr['regdate'] = Func::datetime($arr['regdate']);
                $arr[0]['thumbnail'] = thumbnail($arr);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('pop_total', pop_total($sort_arr));
        $this->set('use_pop', use_pop($sort_arr));
        $this->set('notuse_pop', notuse_pop($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);
    }

}

//
// Controller for display
// https://{domain}/manage/popup/regist
//
class Regist extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/popup/regist.tpl.php');
        $this->layout()->mng_foot();
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makepopForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/popup/regist-submit');
        $form->run();
    }

    public function make()
    {
        $manage = new ManageFunc();

        $this->set('manage', $manage);
    }

}

//
// Controller for submit
// ( Regist )
//
class Regist_submit{

    public function init()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id, title, link, link_target, width, height, pos_top, pos_left, level_from, level_to, show_from, show_to, html, mo_html');
        $manage->req_hidden_inp('post');

        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
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
        Valid::get(
            array(
                'input' => 'width',
                'value' => $req['width'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'height',
                'value' => $req['height'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pos_top',
                'value' => $req['pos_top'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pos_left',
                'value' => $req['pos_left'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'show_from',
                'value' => $req['show_from']
            )
        );
        Valid::get(
            array(
                'input' => 'show_to',
                'value' => $req['show_to']
            )
        );

        if ($req['level_from'] > $req['level_to']) {
            Valid::error('level_to', '노출 종료 level 보다 시작 level이 클 수 없습니다.');
        }

        if ($req['show_from'] > $req['show_to']) {
            Valid::error('show_to', '노출 일자가 올바르지 않습니다.');
        }

        $sql->query(
            "
            select *
            from {$sql->table("popup")}
            where id=:col1
            limit 1
            ",
            array(
                $req['id']
            )
        );

        if ($sql->getcount() > 0) Valid::error('id', '이미 존재하는 팝업 id 입니다.');

        $req['show_to'] = $req['show_to'].' 23:59:59';

        $sql->query(
            "
            insert into {$sql->table("popup")}
            (id, title, link, link_target, width, height, pos_left, pos_top, level_from, level_to, show_from, show_to, html, mo_html, regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, :col11, :col12, :col13, :col14, now())
            ",
            array(
                $req['id'], $req['title'], $req['link'], $req['link_target'], $req['width'], $req['height'], $req['pos_left'],$req['pos_top'],
                $req['level_from'], $req['level_to'], $req['show_from'], $req['show_to'], $req['html'], $req['mo_html']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("popup")}
            where id=:col1
            limit 1
            ",
            array(
                $req['id']
            )
        );
        $idx = $sql->fetch('idx');

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 추가 되었습니다.',
                'location' => PH_MANAGE_DIR.'/popup/modify?idx='.$idx
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/popup/modify
//
class Modify extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/popup/modify.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        $req = Method::request('get', 'idx');

        $sql->query(
            "
            select *
            from {$sql->table("popup")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('팝업이 존재하지 않습니다.');

        $arr = $sql->fetchs();

        $sql->specialchars = 0;
        $sql->nl2br = 0;
        $arr['html'] = $sql->fetch('html');
        $arr['mo_html'] = $sql->fetch('mo_html');

        $arr['show_from'] = substr($arr['show_from'], 0, 10);
        $arr['show_to'] = substr($arr['show_to'], 0, 10);

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
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'modifypopForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/popup/modify-submit');
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
        $req = Method::request('post', 'mode, idx, title, link, link_target, width, height, pos_top, pos_left, level_from, level_to, show_from, show_to, html, mo_html');
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
        Valid::get(
            array(
                'input' => 'width',
                'value' => $req['width'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'height',
                'value' => $req['height'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pos_top',
                'value' => $req['pos_top'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pos_left',
                'value' => $req['pos_left'],
                'check' => array(
                    'charset' => 'number',
                    'minlen' => 1,
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'show_from',
                'value' => $req['show_from']
            )
        );
        Valid::get(
            array(
                'input' => 'show_to',
                'value' => $req['show_to']
            )
        );

        if ($req['level_from'] > $req['level_to']) Valid::error('level_to', '노출 종료 level 보다 시작 level 클 수 없습니다.');
        if ($req['show_from'] > $req['show_to']) Valid::error('show_to', '노출 일자가 올바르지 않습니다.');

        $req['show_to'] = $req['show_to'].' 23:59:59';

        $sql->query(
            "
            update {$sql->table("popup")}
            SET title=:col2, link=:col3, link_target=:col4, width=:col5, height=:col6, pos_top=:col7, pos_left=:col8, level_from=:col9, level_to=:col10, show_from=:col11, show_to=:col12, html=:col13, mo_html=:col14
            where idx=:col1
            ",
            array(
                $req['idx'], $req['title'], $req['link'], $req['link_target'], $req['width'], $req['height'],
                $req['pos_top'], $req['pos_left'], $req['level_from'], $req['level_to'], $req['show_from'], $req['show_to'], $req['html'], $req['mo_html']
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
            delete
            from {$sql->table("popup")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) {
            Valid::error('', '팝업이 존재하지 않습니다.');
        }

        $sql->query(
            "
            delete
            from {$sql->table("popup")}
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
                'location' => PH_MANAGE_DIR.'/popup/result'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}
