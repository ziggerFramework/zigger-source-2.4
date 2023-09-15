<?php
namespace Module\Alarm;

use Corelib\Func;
use Corelib\Method;
use Make\Database\Pdosql;
use Make\Library\Paging;
use Module\Alarm\Library as Alarm_Library;

//
// Module Controller
// ( Received )
//
class Received extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_ALARM_THEME_PATH.'/received.tpl.php');
    }

    public function make()
    {
        $sql = new Pdosql();
        $paging = new Paging();
        $Alarm_Library = new Alarm_Library();

        $req = Method::request('get', 'page');

        Func::getlogin(SET_NOAUTH_MSG);

        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mod:alarm")}
                where to_mb_idx=:col1
                order by idx desc
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
                $sql->specialchars = 1;

                $arr = $sql->fetchs();

                $sql->nl2br = 0;
                $sql->specialchars = 0;
                $arr['memo'] = $sql->fetch('memo');

                $arr['no'] = $paging->getnum();
                $arr['regdate'] = Func::datetime($arr['regdate']);
                $arr[0]['view-link'] = '?mode=read&hash='.$arr['hash'];

                $print_arr[] = $arr;

            } while ($sql->nextRec());

        }

        $this->set('total_new_alarm', Func::number($Alarm_Library->get_new_count()));
        $this->set('print_arr', $print_arr);
        $this->set('pagingprint', $paging->pagingprint(''));
        $this->set('page', $req['page']);
    }

}
