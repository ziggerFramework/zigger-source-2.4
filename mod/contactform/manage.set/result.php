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
// https://{domain}/manage/mod/contactform/result/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_CONTACTFORM_PATH.'/manage.set/html/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function contactform_total($arr)
        {
            return Func::number($arr['contactform_total']);
        }

        function print_name($arr)
        {
            return ($arr['mb_idx'] != 0) ? '<a href="'.PH_MANAGE_DIR.'/member/modify?idx='.$arr['mb_idx'].'">'.$arr['name'].'</a>' : $arr['name'];
        }

        function print_reply($arr)
        {
            return ($arr['rep_idx'] != 0) ? '<strong>완료</strong>' : '대기';
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
            from {$sql->table("mod:contactform")}
            where name is not null
            ) contactform_total
            ", []
        );
        $sort_arr['contactform_total'] = $sql->fetch('contactform_total');

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mod:contactform")}
                where name is not null $sortby $searchby
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
                $arr['article'] = Func::strcut(strip_tags($arr['article']), 0, 30);
                $arr[0]['print_name'] = print_name($arr);
                $arr[0]['print_reply'] = print_reply($arr);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $pagingprint = $paging->pagingprint($manage->pag_def_param());

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('contactform_total', contactform_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);
    }

}

//
// Controller for display
// https://{domain}/manage/mod/contactform/result/view
//
class View extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_CONTACTFORM_PATH.'/manage.set/html/view.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function print_name($arr)
        {
            return ($arr['mb_idx'] != 0) ? '<a href="'.PH_MANAGE_DIR.'/member/modify?idx='.$arr['mb_idx'].'">'.$arr['name'].'</a>' : $arr['name'];
        }

        function print_reply($arr, $reparr)
        {
            return ($arr['rep_idx'] != 0) ? Func::datetime($reparr['regdate']).' 에 답변' : '대기';
        }
    }

    public function make()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        $req = Method::request('get', 'idx');

        Func::add_javascript(PH_PLUGIN_DIR.'/'.PH_PLUGIN_CKEDITOR.'/ckeditor.js');

        $sql->query(
            "
            select *
            from {$sql->table("mod:contactform")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('문의가 존재하지 않습니다.');
        $arr = $sql->fetchs();

        if ($arr['rep_idx'] != 0) {
            $is_reply_show = true;
            $is_reply_btn_show = false;

        } else {
            $is_reply_show = false;
            $is_reply_btn_show = true;
        }

        $view = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $view[$key] = $value;
            }
            $view['regdate'] = Func::datetime($view['regdate']);

        } else {
            $view = null;
        }

        $reparr = array();

        if ($arr['rep_idx'] != 0) {

            $sql->query(
                "
                select *
                from {$sql->table("mod:contactform")}
                where idx=:col1
                limit 1
                ",
                array(
                    $arr['rep_idx']
                )
            );

            $sql->specialchars = 0;
            $sql->nl2br = 0;
            $reparr = $sql->fetchs();

            $repview = array();

            if (isset($reparr)) {
                foreach ($reparr as $key => $value) {
                    $repview[$key] = $value;
                }

            } else {
                $repview = null;
            }

            $this->set('repview', $repview);

        }

        $this->set('manage', $manage);
        $this->set('view', $view);
        $this->set('is_reply_show', $is_reply_show);
        $this->set('is_reply_btn_show', $is_reply_btn_show);
        $this->set('print_name', print_name($arr));
        $this->set('print_reply', print_reply($arr, $reparr));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'viewContactformForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_CONTACTFORM.'/result/view-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( View )
//
class View_submit{

    public function init()
    {
        global $req;

        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, idx, article');
        $manage->req_hidden_inp('post');

        switch ($req['mode']) {
            case 'rep' :
                $this->get_reply();
                break;

            case 'del' :
                $this->get_delete();
                break;
        }
    }

    //
    // reply
    //
    public function get_reply()
    {
        global $CONF, $req;

        $sql = new Pdosql();
        $mail = new Mail();

        Valid::get(
            array(
                'input' => 'article',
                'value' => $req['article']
            )
        );

        $sql->query(
            "
            insert into
            {$sql->table("mod:contactform")}
            (article,regdate)
            values
            (:col1,now())
            ",
            array(
                $req['article']
            )
        );

        $sql->query(
            "
            select idx
            from {$sql->table("mod:contactform")}
            where article=:col1
            ",
            array(
                $req['article']
            )
        );
        $rep_idx = $sql->fetch('idx');

        $sql->query(
            "
            update {$sql->table("mod:contactform")}
            SET rep_idx=:col1
            where idx=:col2
            ",
            array(
                $rep_idx,
                $req['idx']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("mod:contactform")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        $arr = $sql->fetchs();

        $memo = stripslashes($req['article']);
        $memo .= '
            <i>
            <br /><br /><br />
            <strong>문의 내용 :</strong><br />
            '.$arr['article'].'
            </i>
        ';

        $mail->set(
            array(
                'to' => array(
                    [
                        'email' => $arr['email']
                    ]
                ),
                'subject' =>  $CONF['title'].' 에 등록한 문의에 대한 답변입니다.',
                'memo' => $memo
            )
        );
        $mail->send();

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 답변이 발송 되었습니다.'
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
            from {$sql->table("mod:contactform")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Valid::error('', '문의가 존재하지 않습니다.');

        $rep_idx = $sql->fetch('rep_idx');

        $sql->query(
            "
            delete
            from {$sql->table("mod:contactform")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        $sql->query(
            "
            delete
            from {$sql->table("mod:contactform")}
            where idx=:col1
            ",
            array(
                $rep_idx
            )
        );

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 삭제 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mod/'.MOD_CONTACTFORM.'/result/result'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}
