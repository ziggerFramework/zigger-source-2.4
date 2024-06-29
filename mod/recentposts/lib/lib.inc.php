<?php
namespace Module\Recentposts;

use Make\Database\Pdosql;

//
// installation check
//
require_once MOD_RECENTPOSTS_PATH.'/lib/installation.lib.php';
if (Installation::check_installed_module() !== true) Installation::get_auto_install_module();

//
// Module : Recentposts Library
//
class Library {

    // 설정 정보 가져옴
    public function load_conf()
    {

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:recentposts:config'
            ", []
        );

        $conf = array();

        do {
            $cfg = $sql->fetchs();

            $conf[$cfg['cfg_key']] = $cfg['cfg_value'];

        } while($sql->nextRec());

        return $conf;
    }

}
