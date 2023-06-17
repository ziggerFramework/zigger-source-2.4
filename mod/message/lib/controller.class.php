<?php
namespace Module\Message;

use Corelib\Method;

class Make_Controller extends \Controller\Make_Module_Controller {

    public function run()
    {
        $req = Method::request('get', 'mode');

        switch ($req['mode']) {

            case 'received' :
                $run = '\Module\Message\Received';
                break;

            case 'sent' :
                $run = '\Module\Message\Sent';
                break;

            case 'view' :
                $run = '\Module\Message\View';
                break;

            default :
                $run = '\Module\Message\Received';

        }

        $this->configure();
        $$run = new $run();

        if (method_exists($$run, 'func') !== false) {
            $$run->func();
        }

        $$run->init();

    }

}
