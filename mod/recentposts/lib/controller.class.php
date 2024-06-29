<?php
namespace Module\Recentposts;

class Make_Controller extends \Controller\Make_Module_Controller {

    public function run()
    {
        $run = new \Module\Recentposts\Set_recent();
        $run->CONF = $this->configure();

        if (method_exists($run, 'func') !== false) {
            $run->func();
        }

        $run->init();
    }

}
