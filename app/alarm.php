<?php
use Corelib\Func;
use Corelib\Method;
use Make\Database\Pdosql;
use Make\Library\Paging;

//
// Controller for display
// https://{domain}/alarm
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
        $module = new \Module\Alarm\Make_Controller();
        $module->run();
    }

}
