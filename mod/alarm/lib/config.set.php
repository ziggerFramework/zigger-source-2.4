<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_ALARM_CONF = array(
    'dir' => 'alarm', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '알림 모듈' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_ALARM', $MODULE_ALARM_CONF['dir']); // Module 명칭
define('MOD_ALARM_DIR', PH_MOD_DIR.'/'.$MODULE_ALARM_CONF['dir']); // Module 경로
define('MOD_ALARM_PATH', PH_MOD_PATH.'/'.$MODULE_ALARM_CONF['dir']); // Module PHP 경로
define('MOD_ALARM_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_ALARM_CONF['dir']); // Module Theme PHP 경로
define('MOD_ALARM_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_ALARM_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_ALARM_DIR', MOD_ALARM_DIR);
