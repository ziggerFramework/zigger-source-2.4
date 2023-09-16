<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Make\Library\Uploader;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/banner/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/banner/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function bn_total($arr)
        {
            return Func::number($arr['bn_total']);
        }

        function thumbnail($arr)
        {
            if ($arr['pc_img']) {
                $fileinfo = Func::get_fileinfo($arr['pc_img']);
                $tmb = $fileinfo['replink'];

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
                select COUNT(*)
                from {$sql->table("banner")}
            ) bn_total
            ", []
        );
        $sort_arr['bn_total'] = $sql->fetch('bn_total');

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("banner")}
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
                $arr['hit'] = Func::number($arr['hit']);
                $arr['regdate'] = Func::datetime($arr['regdate']);
                $arr[0]['thumbnail'] = thumbnail($arr);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('bn_total' ,bn_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);

    }

}

//
// Controller for display
// https://{domain}/manage/banner/regist
//
class Regist extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/banner/regist.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {
        $manage = new ManageFunc();

        Func::add_javascript(PH_PLUGIN_DIR.'/'.PH_PLUGIN_CKEDITOR.'/ckeditor.js');

        $this->set('manage', $manage);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makebnForm');
        $form->set('type', 'multipart');
        $form->set('action', PH_MANAGE_DIR.'/banner/regist-submit');
        $form->run();
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
        $uploader = new Uploader();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'key, title, link, link_target, zindex');
        $file = Method::request('file', 'pc_img, mo_img');
        $manage->req_hidden_inp('post');

        Valid::get(
            array(
                'input' => 'key',
                'value' => $req['key'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'zindex',
                'value' => $req['zindex'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );

        if (empty($file['pc_img']['name']) || empty($file['mo_img']['name'])) Valid::error('', '배너 이미지가 첨부되지 않았습니다.');

        $uploader->path= PH_DATA_PATH.'/manage';
        $uploader->chkpath();

        $pc_img_name = '';

        if (isset($file['pc_img'])) {
            $uploader->file = $file['pc_img'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('pc_img', '허용되지 않는 PC 배너 이미지 유형입니다.');

            $pc_img_name = $uploader->replace_filename($file['pc_img']['name']);

            if (!$uploader->upload($pc_img_name)) Valid::error('pc_img', 'PC 배너 이미지 업로드 실패');
        }

        $mo_img_name = '';

        if (isset($file['mo_img'])) {
            $uploader->file = $file['mo_img'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('mo_img', '허용되지 않는 모바일 배너 이미지 유형입니다.');

            $mo_img_name = $uploader->replace_filename($file['mo_img']['name']);

            if (!$uploader->upload($mo_img_name)) Valid::error('mo_img', '모바일 배너 이미지 업로드 실패');
        }

        $sql->query(
            "
            insert into {$sql->table("banner")}
            (bn_key, title, link, link_target, pc_img, mo_img, zindex, regdate)
            VALUES
            (:col1, :col2, :col3, :col4, :col5, :col6, :col7, now())
            ",
            array(
                $req['key'], $req['title'], $req['link'], $req['link_target'], $pc_img_name, $mo_img_name, $req['zindex']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("banner")}
            where bn_key=:col1
            order by regdate DESC
            ",
            array(
                $req['key']
            )
        );
        $idx = $sql->fetch('idx');

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 추가 되었습니다.',
                'location' => PH_MANAGE_DIR.'/banner/modify?idx='.$idx
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/banner/modify
//
class Modify extends \Controller\Make_Controller {

    public function init(){
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/banner/modify.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {
        $req = Method::request('get', 'idx');

        $sql = new Pdosql();
        $manage = new ManageFunc();

        $sql->query(
            "
            select *
            from {$sql->table("banner")}
            where idx=binary(:col1)
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('배너가 존재하지 않습니다.');

        $arr = $sql->fetchs();

        $arr['hit'] = Func::number($arr['hit']);

        if ($arr['pc_img'] != '') {
            $is_pc_img_show = true;
            $arr[0]['pc_img'] = Func::get_fileinfo($arr['pc_img']);

        } else {
            $is_pc_img_show = false;
        }

        if ($arr['mo_img'] != '') {
            $is_mo_img_show = true;
            $arr[0]['mo_img'] = Func::get_fileinfo($arr['mo_img']);

        } else {
            $is_mo_img_show = false;
        }

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
        $this->set('is_pc_img_show', $is_pc_img_show);
        $this->set('is_mo_img_show', $is_mo_img_show);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'modifybnForm');
        $form->set('type', 'multipart');
        $form->set('action', PH_MANAGE_DIR.'/banner/modify-submit');
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
        global $req, $file;

        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, idx, key, title, link, link_target, zindex');
        $file = Method::request('file', 'pc_img, mo_img');
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
        global $req, $file;

        $sql = new Pdosql();
        $uploader = new Uploader();

        Valid::get(
            array(
                'input' => 'key',
                'value' => $req['key'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'zindex',
                'value' => $req['zindex'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );

        $uploader->path= PH_DATA_PATH.'/manage';
        $uploader->chkpath();

        $sql->query(
            "
            select *
            from {$sql->table("banner")}
            where idx=binary(:col1)
            limit 1
            ",
            array(
                $req['idx']
            )
        );
        $arr = $sql->fetchs();

        $pc_img_name = '';

        if (isset($file['pc_img'])) {
            $uploader->file = $file['pc_img'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('pc_img', '허용되지 않는 PC 배너 이미지 유형입니다.');

            $pc_img_name = $uploader->replace_filename($file['pc_img']['name']);

            if (!$uploader->upload($pc_img_name)) Valid::error('pc_img', 'PC 배너 이미지 업로드 실패');
        }

        if (isset($file['pc_img']) && $arr['pc_img'] != '') $uploader->drop($arr['pc_img']);
        if ($arr['pc_img'] != '' && !isset($file['pc_img'])) $pc_img_name = $arr['pc_img'];

        $mo_img_name = '';

        if (isset($file['mo_img'])) {
            $uploader->file = $file['mo_img'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('mo_img', '허용되지 않는 모바일 배너 이미지 유형입니다.');

            $mo_img_name = $uploader->replace_filename($file['mo_img']['name']);

            if (!$uploader->upload($mo_img_name)) Valid::error('mo_img', '모바일 배너 이미지 업로드 실패');
        }

        if (isset($file['mo_img']) && $arr['mo_img'] != '') $uploader->drop($arr['mo_img']);
        if ($arr['mo_img'] != '' && empty($file['mo_img']['name'])) $mo_img_name = $arr['mo_img'];

        $sql->query(
            "
            update {$sql->table("banner")}
            set bn_key=:col2, title=:col3, link=:col4, link_target=:col5, pc_img=:col6, mo_img=:col7, zindex=:col8
            where idx=:col1
            ",
            array(
                $req['idx'], $req['key'], $req['title'], $req['link'], $req['link_target'], $pc_img_name, $mo_img_name, $req['zindex']
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
        $uploader = new Uploader();
        $manage = new ManageFunc();

        $sql->query(
            "
            select *
            from {$sql->table("banner")}
            where idx=binary(:col1)
            limit 1
            ",
            array(
                $req['idx']
            )
        );
        $arr = $sql->fetchs();

        if ($sql->getcount() < 1) Valid::error('', '배너가 존재하지 않습니다.');

        $uploader->path= PH_DATA_PATH.'/manage';

        if ($arr['pc_img']) $uploader->drop($arr['pc_img']);
        if ($arr['mo_img']) $uploader->drop($arr['mo_img']);

        $sql->query(
            "
            delete
            from {$sql->table("banner")}
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
                'location' => PH_MANAGE_DIR.'/banner/result'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}
