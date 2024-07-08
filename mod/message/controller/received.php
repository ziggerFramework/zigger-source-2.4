<?php
namespace Module\Message;

use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Module\Message\Library as Message_Library;

//
// Module Controller
// ( Received )
//
class Received extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_MESSAGE_THEME_PATH.'/received.tpl.php');
    }

    public function func()
    {
        function exp_keywords($keyword) {
            $exp = explode(' ', $keyword);

            $key_arr = array();

            for ($i = 0; $i < count($exp); $i++) {
                $key_arr[$i] = $exp[$i];
            }

            return $key_arr;
        }
    }

    public function make()
    {
        $sql = new Pdosql();
        $paging = new Paging();
        $Message_Library = new Message_Library();

        $req = Method::request('get', 'page, where, keyword');

        Func::getlogin();

        // 검색 처리
        $where = (isset($req['where']) && !empty($req['where']) && in_array($req['where'], ['mb_id', 'mb_name', 'article'])) ? $req['where'] : '';
        $keyword = (!empty($where) && !empty(trim($req['keyword']))) ? addslashes(urldecode($req['keyword'])) : '';
        
        $searchby = '';
        if (!empty($where) && !empty($keyword)) {
            $searchby = array();
            
            foreach (exp_keywords($keyword) as $key => $value) {
                $searchby[] = '`'.$where.'` like \'%'.addslashes($value).'%\'';
            }
            $searchby = 'and ('.implode(' and ', $searchby).')';
        }

        // 메시지 목록
        $sql->query(
            $paging->query(
                "
                select message.*, member.mb_name, member.mb_id
                from {$sql->table("mod:message")} as message
                left outer join
                {$sql->table("member")} as member
                on message.from_mb_idx=member.mb_idx
                where message.to_mb_idx=:col1 and message.msg_type=:col2 and message.dregdate is null {$searchby}
                order by message.idx desc
                ",
                array(
                    MB_IDX, 'received'
                )
            )
        );
        $print_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $sql->nl2br = 1;

                $arr = $sql->fetchs();

                $sql->nl2br = 0;
                $arr['article'] = $sql->fetch('article');

                $arr['no'] = $paging->getnum();
                $arr['mb_id'] = Func::strcut($arr['mb_id'], 0, 15);
                $arr['article'] = Func::strcut($arr['article'], 0, 50);
                $arr['regdate'] = Func::date($arr['regdate']);
                $arr['chked'] = Func::date($arr['chked']);
                $arr[0]['view-link'] = Func::get_param_combine('mode=view&refmode=received&where='.$where.'&keyword='.$keyword.'&hash='.$arr['hash'].'&page='.$req['page'], '?');

                $print_arr[] = $arr;

            } while ($sql->nextRec());

        }

        $this->set('total_new_message', Func::number($Message_Library->get_new_count()));
        $this->set('print_arr', $print_arr);
        $this->set('pagingprint', $paging->pagingprint('&mode=received&where='.$where.'&keyword='.$keyword));
        $this->set('where', $where);
        $this->set('keyword', $keyword);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action',  MOD_MESSAGE_DIR.'/controller/received/received-submit');
        $form->run();
    }

    public function message_tab()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', MOD_MESSAGE_PATH.'/controller/message.tab.inc.php');
        $fetch->set('className', 'Module\Message\message_tab_inc');
        $fetch->run();
    }

}

//
// Controller for submit
// ( Received )
//
class Received_submit {

    public function init()
    {
        global $req;

        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, cnum');

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
            $cnum[] = "hash='".addslashes($value)."'";
        }

        $where = implode(' or ', $cnum);

        // 데이터 삭제
        $sql->query(
            "
            update {$sql->table("mod:message")}
            set dregdate=now()
            where msg_type=:col1 and to_mb_idx=:col2 and {$where}
            ",
            array(
                'received', MB_IDX
            )
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
