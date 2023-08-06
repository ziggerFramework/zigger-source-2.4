<?php
namespace Module\Alarm;

use Corelib\Func;
use Corelib\Method;
use Make\Database\Pdosql;
use Module\Alarm\Library as Alarm_Library;

//
// Module Controller
// ( Read )
//
class Read extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view();
    }

    public function make()
    {
        $sql = new Pdosql();

        $req = Method::request('get', 'hash, allcheck, page');

        Func::getlogin(SET_NOAUTH_MSG);

        // 전체 읽음 처리
        if ($req['allcheck'] == 1) {
            $sql->query(
                "
                update
                {$sql->table("mod:alarm")} set
                chked='Y'
                where to_mb_idx=:col1
                ",
                array(
                    MB_IDX
                )
            );

            Func::location('?page='.$req['page']);
        }

        // 단일 읽음 처리
        else {
            $sql->query(
                "
                select *
                from {$sql->table("mod:alarm")}
                where to_mb_idx=:col1 and hash=:col2
                ",
                array(
                    MB_IDX, $req['hash']
                )
            );

            if ($sql->getcount() < 1) Func::err_back('알림이 존재하지 않습니다.');

            $arr = $sql->fetchs();

            $sql->specialchars = 0;
            $arr['href'] = $sql->fetch('href');

            $sql->query(
                "
                update
                {$sql->table("mod:alarm")} set
                chked='Y'
                where to_mb_idx=:col1 and hash=:col2
                ",
                array(
                    MB_IDX, $req['hash']
                )
            );

            Func::location(PH_DIR.$arr['href']);
        }
    }

}
