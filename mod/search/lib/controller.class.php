<?php
namespace Module\Search;

class Make_Controller extends \Controller\Make_Module_Controller {

    public function run()
    {
        $run = new \Module\Search\Search();
        $this->configure();

        if (method_exists($run, 'func') !== false) {
            $run->func();
        }

        $run->init();
    }

}
