<?php

//
// Controller for display
// https://{domain}/error/code404
//
class code404 extends \Controller\Make_Controller {

    public function init()
    {
        $this->common()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/error_404.tpl.php');
        $this->common()->foot();
    }

    public function make()
    {
        global $CONF;

        $this->set('go_site', $CONF['domain']);
    }

}
