<?php
namespace Module\Board;

use Corelib\Method;

class Make_Controller extends \Controller\Make_Module_Controller {

    public function run()
    {
        $req = Method::request('get', 'mode');

        switch ($req['mode']) {

            case 'view' :
                $run = '\Module\Board\View';
                break;

            case 'write' :
                $run = '\Module\Board\Write';
                break;

            case 'delete' :
                $run = '\Module\Board\Delete';
                break;

            default :
                $run = '\Module\Board\Result';

        }

        $this->configure();
        $$run = new $run();

        if (method_exists($$run, 'func') !== false) {
            $$run->func();
        }

        $$run->init();

    }

}
