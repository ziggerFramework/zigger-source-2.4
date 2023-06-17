<?php

//
// Controller for display
// https://{domain}/sub/view/contents
//
class Contents extends \controller\Make_Controller {

    public function init()
    {
        $this->layout()->category_key(2);
        $this->layout()->head();
        $this->layout()->view();
        $this->layout()->foot();
    }

    public function make()
    {
        $this->module();
    }

    public function module()
    {
        $module = new \Module\Contents\Make_Controller();
        $module->set('key', 'sample');
        $module->run();
    }

}

//
// Controller for display
// https://{domain}/sub/view/manager
//
class Manager extends \controller\Make_Controller {

    public function init()
    {
        $this->layout()->category_key(3);
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/sub/manager.tpl.php'); // view(template) 파일과 결합하는 경우 view 경로 지정
        $this->layout()->foot();
    }

    public function make()
    {

    }

}

//
// Controller for display
// https://{domain}/sub/view/contactus
//
class Contactus extends \controller\Make_Controller {

    public function init()
    {
        $this->layout()->category_key(7);
        $this->layout()->head();
        $this->layout()->view();
        $this->layout()->foot();
    }

    public function make()
    {
        $this->module();
    }

    public function module()
    {
        $module = new \Module\Contactform\Make_Controller();
        $module->run();
    }

}
