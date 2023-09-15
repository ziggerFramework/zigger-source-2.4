<?php
namespace Module\Message;

use Corelib\Func;
use Corelib\Method;
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

    public function make()
    {
        $sql = new Pdosql();
        $paging = new Paging();
        $Message_Library = new Message_Library();

        $req = Method::request('get', 'page');

        Func::getlogin(SET_NOAUTH_MSG);

        $sql->query(
            $paging->query(
                "
                select message.*, member.mb_name, member.mb_id
                from {$sql->table("mod:message")} as message
                left outer join
                {$sql->table("member")} as member
                on message.from_mb_idx=member.mb_idx
                where message.to_mb_idx=:col1
                order by message.idx desc
                ",
                array(
                    MB_IDX
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
                $arr[0]['view-link'] = Func::get_param_combine('mode=view&refmode=received&hash='.$arr['hash'].'&page='.$req['page'], '?');

                $print_arr[] = $arr;

            } while ($sql->nextRec());

        }

        $this->set('total_new_message', Func::number($Message_Library->get_new_count()));
        $this->set('print_arr', $print_arr);
        $this->set('pagingprint', $paging->pagingprint('&mode=received'));
    }

    public function message_tab()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', MOD_MESSAGE_PATH.'/controller/message.tab.inc.php');
        $fetch->set('className', 'Module\Message\message_tab_inc');
        $fetch->run();
    }

}
