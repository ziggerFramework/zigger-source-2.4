<?php
namespace Module\Contents;

use Corelib\Func;
use Make\Database\Pdosql;

//
// Module Controller
// ( View )
//
class View extends \Controller\Make_Controller {

    public function init()
    {
        global $CONF, $MOD_CONF;

        $sql = new Pdosql();

        $sql->specialchars = 0;
        $sql->nl2br = 0;

        $sql->query(
            "
            select *
            from {$sql->table("mod:contents")}
            where data_key=:col1
            ",
            array(
                $MOD_CONF['key']
            )
        );

        if ($sql->getcount() < 1) unc::core_err('존재하지 않는 콘텐츠 key 입니다. : \''.$MOD_CONF['key'].'\'');

        $print_arr = $sql->fetchs();

        echo '<div id="mod-contents-wrap" class="nostyle">'.PHP_EOL;
        echo (Func::chkdevice() == 'mobile' && $print_arr['use_mo_html'] == 'Y' && $CONF['use_mobile'] == 'Y') ? $print_arr['mo_html'] : $print_arr['html'];
        echo '</div>'.PHP_EOL;
    }

}
