<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_MESSAGE_CONF = array(
    'dir' => 'message', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '메시지 모듈' // 모듈 명칭
);

////////////////////////////////////////////////////

//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_MESSAGE', $MODULE_MESSAGE_CONF['dir']); // Module 명칭
define('MOD_MESSAGE_DIR', PH_MOD_DIR.'/'.$MODULE_MESSAGE_CONF['dir']); // Module 경로
define('MOD_MESSAGE_PATH', PH_MOD_PATH.'/'.$MODULE_MESSAGE_CONF['dir']); // Module PHP 경로
define('MOD_MESSAGE_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_MESSAGE_CONF['dir']); // Module Theme PHP 경로
define('MOD_MESSAGE_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_MESSAGE_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_MESSAGE_DIR', MOD_MESSAGE_DIR);
