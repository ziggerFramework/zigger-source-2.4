<?php

//
// Controller for display
// https://{domain}/search
//
class index extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view();
        $this->layout()->foot();
    }

    public function make()
    {
        $this->module();
    }

    public function module(){
        $module = new \Module\Search\Make_Controller();
        $module->run();
    }

}
