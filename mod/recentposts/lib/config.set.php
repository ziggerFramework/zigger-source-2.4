<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_RECENTPOSTS_CONF = array(
    'dir' => 'recentposts', // 모듈 식별값 (모듈 디렉토리명)
    'title' => 'Recent Posts' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_RECENTPOSTS', $MODULE_RECENTPOSTS_CONF['dir']); // Module 명칭
define('MOD_RECENTPOSTS_DIR', PH_MOD_DIR.'/'.$MODULE_RECENTPOSTS_CONF['dir']); // Module 경로
define('MOD_RECENTPOSTS_PATH', PH_MOD_PATH.'/'.$MODULE_RECENTPOSTS_CONF['dir']); // Module PHP 경로
define('MOD_RECENTPOSTS_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_RECENTPOSTS_CONF['dir']); // Module Theme PHP 경로
define('MOD_RECENTPOSTS_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_RECENTPOSTS_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_RECENTPOSTS_DIR', MOD_RECENTPOSTS_DIR);
