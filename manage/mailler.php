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
// https://{domain}/manage/mailler/template
//
class Template extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/template.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function tpl_total($arr)
        {
            return Func::number($arr['total']);
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
                from {$sql->table("mailtpl")}
            ) total
            ", []
        );
        $sort_arr['total'] = $sql->fetch('total');

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mailtpl")}
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

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('tpl_total', tpl_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);

    }

}

//
// Controller for display
// https://{domain}/manage/mailler/regist
//
class Regist extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/regist.tpl.php');
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
        $form->set('id', 'maketplForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mailler/regist-submit');
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
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'type, title, html');
        $manage->req_hidden_inp('post');

        Valid::get(
            array(
                'input' => 'type',
                'value' => $req['type'],
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
            from {$sql->table("mailtpl")}
            where type=:col1
            order by regdate DESC
            ",
            array(
                $req['type']
            )
        );

        if ($sql->getcount() > 0) Valid::error('type', '이미 존재하는 템플릿 type 입니다.');

        $sql->query(
            "
            insert into {$sql->table("mailtpl")}
            (type, title, html, regdate)
            VALUES
            (:col1, :col2, :col3, now())
            ",
            array(
                $req['type'], $req['title'], $req['html']
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("mailtpl")}
            where type=:col1
            order by regdate DESC
            ",
            array(
                $req['type']
            )
        );
        $idx = $sql->fetch('idx');

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 추가 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mailler/modify?idx='.$idx
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mailler/modify
//
class Modify extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/modify.tpl.php');
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
            from {$sql->table("mailtpl")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) {
            Func::err_back('템플릿이 존재하지 않습니다.');
        }

        $arr = $sql->fetchs();
        $sql->specialchars = 1;
        $sql->nl2br = 0;
        $arr['html'] = $sql->fetch('html');

        $write = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value){
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
        $form->set('id', 'modifytplForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mailler/modify-submit');
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
        $req = Method::request('post', 'mode, idx, title, html');
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

        $sql->query(
            "
            select *
            from {$sql->table("mailtpl")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );

        if ($sql->fetch('system') == 'Y') {

            $sql->query(
                "
                update {$sql->table("mailtpl")}
                set html=:col1
                where idx=:col2
                ",
                array(
                    $req['html'], $req['idx']
                )
            );

        } else {

            $sql->query(
                "
                update {$sql->table("mailtpl")}
                set title=:col1,html=:col2
                where idx=:col3
                ",
                array(
                    $req['title'], $req['html'], $req['idx']
                )
            );

        }

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
            from {$sql->table("mailtpl")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Valid::error('', '메일 템플릿이 존재하지 않습니다.');
        if ($sql->fetch('system') == 'Y') Valid::error('', '시스템 발송 메일 템플릿은 삭제 불가합니다.');

        $sql->query(
            "
            delete
            from {$sql->table("mailtpl")}
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
                'location' => PH_MANAGE_DIR.'/mailler/template'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mailler/send
//
class Send extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/send.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function tpl_opts($type = 'title')
        {
            $sql = new Pdosql();

            $sql->query(
                "
                select *
                from {$sql->table("mailtpl")}
                where system='N' or type='default'
                order by type asc
                ", []
            );

            $opts = '';
            $opts_source = array();

            if ($sql->getcount() > 0) {
                do {
                    $sql->specialchars = 1;
                    $sql->nl2br = 1;
                    $arr = $sql->fetchs();
                    $sql->specialchars = 0;
                    $sql->nl2br = 0;
                    $arr['html'] = $sql->fetch('html');

                    $opts .= '<option value="'.$arr['type'].'">'.$arr['type'].' ('.$arr['title'].')</option>';
                    $opts_source[$arr['type']] = $arr['html'];
                    
                } while ($sql->nextRec());
            }

            return ($type == 'title') ? $opts : $opts_source;
        }
    }

    public function make()
    {
        $manage = new ManageFunc();

        $req = Method::request('get', 'mailto');

        $this->set('manage', $manage);
        $this->set('mailto', $req['mailto']);
        $this->set('tpl_opts', tpl_opts('title'));
        $this->set('tpl_opts_source', tpl_opts('source'));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'sendmailForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mailler/send-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Send )
//
class Send_submit{

    public function init()
    {
        $sql = new Pdosql();
        $mail = new Mail();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'type, to_mb, level_from, level_to, subject, html');
        $manage->req_hidden_inp('post');

        if ($req['type'] == 1) {
            Valid::get(
                array(
                    'input' => 'to_mb',
                    'value' => $req['to_mb']
                )
            );

        } else if ($req['level_from'] > $req['level_to']) {
            Valid::error('level_to', '수신 종료 level 보다 시작 level이 클 수 없습니다.');
        }

        Valid::get(
            array(
                'input' => 'subject',
                'value' => $req['subject']
            )
        );
        Valid::get(
            array(
                'input' => 'html',
                'value' => $req['html']
            )
        );

        $rcv_email = array();

        if ($req['type'] == 1) {

            $sql->query(
                "
                select *
                from {$sql->table("member")}
                where mb_id=:col1 and mb_dregdate is null
                ",
                array(
                    $req['to_mb']
                )
            );

            if ($sql->getcount() < 1) Valid::error('', '존재하지 않는 회원 id 입니다.');
            if (!$sql->fetch('mb_email')) Valid::error('', '회원 email이 등록되어 있지 않습니다.');

            $rcv_email[] = array(
                'email' => $sql->fetch('mb_email')
            );
            $req['level_from'] = 0;
            $req['level_to'] = 0;
        }

        if ($req['type'] == 2) {

            $sql->query(
                "
                select *
                from {$sql->table("member")}
                where mb_level>=:col1 and mb_level<=:col2 and mb_dregdate is null
                order by mb_idx asc
                ",
                array(
                    $req['level_from'],
                    $req['level_to']
                )
            );

            if ($sql->getcount() < 1) Valid::error('', '범위내 수신할 회원이 존재하지 않습니다.');

            do {
                $rcv_email[] = array(
                    'email' => $sql->fetch('mb_email')
                );

            } while ($sql->nextRec());

            $req['to_mb'] = '';
        }

        $mail->set(
            array(
                'tpl' => null,
                'to' => $rcv_email,
                'subject' => $req['subject'],
                'memo' => stripslashes($req['html'])
            )
        );
        $mail->send();

        $sql->query(
            "
            insert into {$sql->table("sentmail")}
            (to_mb, level_from, level_to, subject, html, regdate)
            VALUES
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                $req['to_mb'], $req['level_from'], $req['level_to'], $req['subject'], $req['html']
            )
        );

        $sql->query(
            "
            select idx
            from {$sql->table("sentmail")}
            where subject=:col1
            order by regdate DESC
            limit 1
            ",
            array(
                $req['subject']
            )
        );

        $idx = $sql->fetch('idx');

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 발송 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mailler/historyview?idx='.$idx
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mailler/history
//
class History extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/history.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function sent_total($arr)
        {
            return Func::number($arr['total']);
        }

        function to_mb_total($arr)
        {
            return Func::number($arr['to_mb_total']);
        }

        function level_from_total($arr)
        {
            return Func::number($arr['level_from_total']);
        }

        function print_level($arr)
        {
            global $MB;

            return ($arr['to_mb']) ? '특정 회원 지정' : $arr['level_from'].' ('.$MB['type'][$arr['level_from']].') ~ '.$arr['level_to'].' ('.$MB['type'][$arr['level_to']].')';
        }

        function print_to_mb($arr)
        {
            global $MB;

            return (!$arr['to_mb']) ? '수신 범위 지정' : $arr['to_mb'];
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
                from {$sql->table("sentmail")}
            ) total,
            (
                select count(*)
                from {$sql->table("sentmail")}
                where to_mb is not null and to_mb!=''
            ) to_mb_total,
            (
                select count(*)
                from {$sql->table("sentmail")}
                where to_mb is null or to_mb=''
            ) level_from_total
            ", []
        );

        $sort_arr['total'] = $sql->fetch('total');
        $sort_arr['to_mb_total'] = $sql->fetch('to_mb_total');
        $sort_arr['level_from_total'] = $sql->fetch('level_from_total');

        switch ($PARAM['sort']) {
            case 'to_mb' :
                $sortby = 'and to_mb is not null and to_mb!=\'\'';
                break;

            case 'level_from' :
                $sortby = 'and to_mb is null or to_mb=\'\'';
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
                from {$sql->table("sentmail")}
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
                $arr[0]['print_level'] = print_level($arr);
                $arr[0]['print_to_mb'] = print_to_mb($arr);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('sent_total', sent_total($sort_arr));
        $this->set('to_mb_total', to_mb_total($sort_arr));
        $this->set('level_from_total', level_from_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mailler/history-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( History )
//
class History_submit {

    public function init()
    {
        global $req;

        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, cnum');
        $manage->req_hidden_inp('post');

        if (!isset($req['mode']) || !$req['mode']) Valid::error('', '필수 값이 누락 되었습니다.');

        // 선택 항목 검사
        if (!isset($req['cnum']) || !$req['cnum'] || !is_array($req['cnum'])) Valid::error('', '선택된 항목이 없습니다.');

        switch ($req['mode']) {

            case 'del' :
                $this->get_del();
                break;

        }
    }

    public function get_del()
    {
        global $req;

        $sql = new Pdosql();

        // where 조합
        $cnum = array();

        foreach ($req['cnum'] as $key => $value) {
            $cnum[] = "idx='".addslashes($value)."'";
        }

        $where = implode(' or ', $cnum);

        // 데이터 삭제
        $sql->query(
            "
            delete
            from {$sql->table("sentmail")}
            where {$where} 
            ", []
        );

        // return
        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 삭제 되었습니다.'
            )
        );
        Valid::turn();

    }

}

//
// Controller for display
// https://{domain}/manage/mailler/historyview
//
class Historyview extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/mailler/historyview.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function print_level($arr)
        {
            global $MB;

            return ($arr['to_mb']) ? '' : $arr['level_from'].' ('.$MB['type'][$arr['level_from']].') ~ '.$arr['level_to'].' ('.$MB['type'][$arr['level_to']].')';
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
            from {$sql->table("sentmail")}
            where idx=:col1
            limit 1
            ",
            array(
                $req['idx']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('메일 발송 내역이 존재하지 않습니다.');

        $arr = $sql->fetchs();
        $sql->specialchars = 0;
        $sql->nl2br = 0;
        $arr['html'] = $sql->fetch('html');
        $arr['regdate'] = Func::datetime($arr['regdate']);

        $is_level_show = false;
        $is_to_mb_show = false;

        if ($arr['to_mb']) {
            $is_to_mb_show = true;

        } else {
            $is_level_show = true;
        }

        $view = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $view[$key] = $value;
            }

        } else {
            $view = null;
        }

        $this->set('manage', $manage);
        $this->set('view', $view);
        $this->set('is_level_show', $is_level_show);
        $this->set('is_to_mb_show', $is_to_mb_show);
        $this->set('print_level', print_level($arr));
    }

}
