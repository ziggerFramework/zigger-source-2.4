<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_BOARD_CONF = array(
    'dir' => 'board', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '게시판 모듈' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_BOARD', $MODULE_BOARD_CONF['dir']); // Module 명칭
define('MOD_BOARD_DIR', PH_MOD_DIR.'/'.$MODULE_BOARD_CONF['dir']); // Module 경로
define('MOD_BOARD_PATH', PH_MOD_PATH.'/'.$MODULE_BOARD_CONF['dir']); // Module PHP 경로
define('MOD_BOARD_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_BOARD_CONF['dir']); // Module Theme PHP 경로
define('MOD_BOARD_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_BOARD_CONF['dir']); // Module Theme PHP 경로
define('MOD_BOARD_DATA_DIR', PH_DIR.'/data/'.$MODULE_BOARD_CONF['dir']); // Module Data PHP 경로
define('MOD_BOARD_DATA_PATH', PH_PATH.'/data/'.$MODULE_BOARD_CONF['dir']); // Module Data PHP 경로
Func::define_javascript('MOD_BOARD_DIR', MOD_BOARD_DIR);
