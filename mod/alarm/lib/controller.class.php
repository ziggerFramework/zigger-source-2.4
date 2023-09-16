<?php
namespace Module\Alarm;

use Corelib\Method;

class Make_Controller extends \Controller\Make_Module_Controller {

    public function run()
    {
        $req = Method::request('get', 'mode');

        switch ($req['mode']) {

            case 'read' :
                $run = '\Module\Alarm\Read';
                break;

            default :
                $run = '\Module\Alarm\Received';

        }

        $run = new $run();
        $this->configure();

        if (method_exists($run, 'func') !== false) {
            $run->func();
        }

        $run->init();

    }

}
