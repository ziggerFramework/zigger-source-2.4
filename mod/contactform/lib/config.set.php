<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_CONTACTFORM_CONF = array(
    'dir' => 'contactform', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '온라인문의 모듈' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_CONTACTFORM', $MODULE_CONTACTFORM_CONF['dir']); // Module 명칭
define('MOD_CONTACTFORM_DIR', PH_MOD_DIR.'/'.$MODULE_CONTACTFORM_CONF['dir']); // Module 경로
define('MOD_CONTACTFORM_PATH', PH_MOD_PATH.'/'.$MODULE_CONTACTFORM_CONF['dir']); // Module PHP 경로
define('MOD_CONTACTFORM_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_CONTACTFORM_CONF['dir']); // Module Theme PHP 경로
define('MOD_CONTACTFORM_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_CONTACTFORM_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_CONTACTFORM_DIR', MOD_CONTACTFORM_DIR);
