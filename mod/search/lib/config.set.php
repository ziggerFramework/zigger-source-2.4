<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_SEARCH_CONF = array(
    'dir' => 'search', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '통합검색' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_SEARCH', $MODULE_SEARCH_CONF['dir']); // Module 명칭
define('MOD_SEARCH_DIR', PH_MOD_DIR.'/'.$MODULE_SEARCH_CONF['dir']); // Module 경로
define('MOD_SEARCH_PATH', PH_MOD_PATH.'/'.$MODULE_SEARCH_CONF['dir']); // Module PHP 경로
define('MOD_SEARCH_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_SEARCH_CONF['dir']); // Module Theme PHP 경로
define('MOD_SEARCH_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_SEARCH_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_SEARCH_DIR', MOD_SEARCH_DIR);
