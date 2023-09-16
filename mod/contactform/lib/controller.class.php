<?php
namespace Module\Contactform;

class Make_Controller extends \Controller\Make_Module_Controller
{

    public function run()
    {
        $run = new \Module\Contactform\Form();
        $this->configure();

        if (method_exists($run, 'func') !== false) {
            $run->func();
        }

        $run->init();
    }

}
