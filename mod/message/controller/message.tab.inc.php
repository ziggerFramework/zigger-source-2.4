<?php
namespace Module\Message;

use Corelib\Method;

//
// Module Controller
// ( Message_tab_inc )
//
class Message_tab_inc extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_MESSAGE_THEME_PATH.'/message.tab.inc.tpl.php');
    }

    public function make()
    {
        $req = Method::request('get', 'mode, refmode');

        $tab_active = '';

        if ($req['mode'] != 'view') {
            $tab_active = ($req['mode']) ? $req['mode'] : 'received';

        } else {
            $tab_active = $req['refmode'];
        }

        $this->set('tab_active', $tab_active);
    }

}
