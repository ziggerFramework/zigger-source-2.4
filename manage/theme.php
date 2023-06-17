<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/theme/theme
//
class Theme extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/theme/theme.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function theme_total()
        {
            global $THEME;

            return count($THEME);
        }

        function thumbnail($arr)
        {
            return PH_DIR.'/theme/'.$arr['theme'].'/theme.thumb.jpg';
        }
    }

    public function make()
    {
        global $THEME;

        $manage = new ManageFunc();

        $print_arr = array();

        foreach ($THEME as $key => $value) {

            $xml_file = PH_PATH.'/theme/'.$value.'/theme.info.xml';

            if (file_exists($xml_file)) $load_xml = simplexml_load_file($xml_file);

            $xml_arr = array();
            $xml_arr['theme'] = $value;
            $xml_arr['name'] = $load_xml[0]->name;
            $xml_arr['developer'] = $load_xml[0]->developer;
            $xml_arr['version'] = $load_xml[0]->version;
            $xml_arr['develDate'] = $load_xml[0]->develDate;
            $xml_arr['updateDate'] = $load_xml[0]->updateDate;
            $xml_arr['website'] = $load_xml[0]->website;
            $xml_arr[0]['thumbnail'] = thumbnail($xml_arr);

            $print_arr[] = $xml_arr;

            $this->set('manage', $manage);
            $this->set('theme_total', theme_total());
            $this->set('print_arr', $print_arr);
        }
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'themeForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/theme/theme-submit');
        $form->run();
    }
}

//
// Controller for submit
// ( Theme )
//
class Theme_submit{

    public function init()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'theme_slt');
        $manage->req_hidden_inp('post');

        $sql->query(
            "
            update
            {$sql->table("config")}
            set cfg_value=:col1
            where cfg_type='engine' and cfg_key='theme'
            ", array(
                $req['theme_slt']
            )
        );

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 변경 되었습니다.'
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/theme/module
//
class Module extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/theme/module.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function module_total()
        {
            global $MODULE;

            return count($MODULE);
        }
    }

    public function make()
    {
        global $MODULE;

        $manage = new ManageFunc();

        $print_arr = array();

        foreach ($MODULE as $key => $value) {

            $xml_file = PH_PATH.'/mod/'.$value.'/manage.set/module.info.xml';

            if (file_exists($xml_file)) $load_xml = simplexml_load_file($xml_file);

            $xml_arr = array();
            $xml_arr['module'] = $value;
            $xml_arr['name'] = $load_xml[0]->name;
            $xml_arr['developer'] = $load_xml[0]->developer;
            $xml_arr['version'] = $load_xml[0]->version;
            $xml_arr['develDate'] = $load_xml[0]->develDate;
            $xml_arr['updateDate'] = $load_xml[0]->updateDate;
            $xml_arr['website'] = $load_xml[0]->website;

            $json_file = PH_MOD_PATH.'/'.$value.'/manage.set/navigator.json';

            if (file_exists($json_file)) {
                $load_json = json_decode(file_get_contents($json_file), true);
                $xml_arr['golink'] = $load_json[0]['href'];
            }

            $print_arr[] = $xml_arr;

            $this->set('module_total', module_total());
            $this->set('print_arr', $print_arr);
            $this->set('manage', $manage);

        }
    }
}
