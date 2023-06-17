<?php

//
// Controller for display
// https://{domain}/sub/board/view
//
class News extends \controller\Make_Controller {

    public function init()
    {
        $this->layout()->category_key(5);
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
        $module = new \Module\Board\Make_Controller();
        $module->set('id', 'news');
        $module->run();
    }

}

//
// Controller for display
// https://{domain}/sub/board/free
//
class Free extends \controller\Make_Controller {

    public function init()
    {
        $this->layout()->category_key(6);
        $this->layout()->head();
        $this->layout()->view();
        $this->layout()->foot();
    }

    public function make(){
        $this->module();
    }

    public function module(){
        $module = new \Module\Board\Make_Controller();
        $module->set('id', 'freeboard');
        $module->run();
    }

}
