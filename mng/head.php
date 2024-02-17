<?php
use Corelib\Func;
use Manage\ManageFunc;

$manage = new ManageFunc();

require_once PH_MANAGE_PATH.'/head.set.php';

$gnb_arr = array();

foreach ($MODULE as $key => $value) {

    $xml_file = PH_MOD_PATH.'/'.$value.'/manage.set/module.info.xml';

    if (file_exists($xml_file)) {
        $mod_xml = simplexml_load_file($xml_file);

        $gnb_arr[$key]['mod'] = $MODULE[$key];
        $gnb_arr[$key]['name'] = $mod_xml[0]->name;
    }

    // navigator json file
    $json_file = PH_MOD_PATH.'/'.$value.'/manage.set/navigator.json';

    if (file_exists($json_file)) {
        $mod_json = json_decode(file_get_contents($json_file), true);

        for ($i = 0; $i < count($mod_json); $i++) {
            $gnb_arr[$key][$i]['href'] = $mod_json[$i]['href'];

            $gnb_arr[$key][$i]['data-idx-href'] = '';

            if (isset($mod_json[$i]['data-idx-href'])) $gnb_arr[$key][$i]['data-idx-href'] = $mod_json[$i]['data-idx-href'];

            $gnb_arr[$key][$i]['title'] = $mod_json[$i]['title'];
        }
    }

    // js file
    $js_file = '/'.$value.'/manage.set/manage.js';
    if (file_exists(PH_MOD_PATH.$js_file)) Func::add_javascript(PH_MOD_DIR.$js_file);

    // css file
    $css_file = '/'.$value.'/manage.set/manage.css';
    if (file_exists(PH_MOD_PATH.$css_file)) Func::add_stylesheet(PH_MOD_DIR.$css_file);

}

require_once PH_MANAGE_PATH.'/html/head.tpl.php';
