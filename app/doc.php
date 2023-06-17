<?php

//
// Controller for display
// https://{domain}/doc/terms-of-service
//
class Terms_of_service extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/doc/terms-of-service.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {

    }

}

//
// Controller for display
// https://{domain}/doc/privacy-policy
//
class Privacy_policy extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/doc/privacy-policy.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {

    }

}
