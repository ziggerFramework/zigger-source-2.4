<?php
namespace Corelib;

use Corelib\Func;
use Make\Database\Pdosql;

class Blocked {

    static public function get_qry()
    {
        global $ip_qry;

        $ip_ex = explode('.', $_SERVER['REMOTE_ADDR']);
        $ip_qry = array();

        for ($i = 0; $i < count($ip_ex); $i++) {
            $ip_rpt_txt = '';
            $ip_rpt_ip = '';

            $ip_rpt = 4 - ($i + 1);

            for ($j = 0; $j < $ip_rpt; $j++) {
                $ip_rpt_txt .= '.*';
            }

            for ($k = 0; $k <= $i; $k++) {
                $ip_rpt_ip .= '.'.$ip_ex[$k];
            }

            $ip_qry[$i] = substr($ip_rpt_ip, 1).$ip_rpt_txt;
        }
    }

    static public function chk_block()
    {
        global $MB, $ip_qry;

        $localhosts = array('127.0.0.1', '::1', 'localhost', '255.255.255.0');

        if (in_array($_SERVER['REMOTE_ADDR'], $localhosts)) return false;

        $sql = new Pdosql();

        self::get_qry();

        $sql->query(
            "
            select *
            from {$sql->table("blockmb")}
            where (ip=:col1 or ip=:col2 or ip=:col3 or ip=:col4) or (mb_idx=:col5 and mb_id=:col6)
            ",
            array(
                $ip_qry[0],
                $ip_qry[1],
                $ip_qry[2],
                $ip_qry[3],
                $MB['idx'],
                $MB['id']
            )
        );

        $uri = Func::thisuri();
        $loc_page = PH_DIR.'/member/warning';

        if ($sql->getcount() > 0 && $uri != $loc_page) Func::location($loc_page);

    }
}

Blocked::chk_block();
