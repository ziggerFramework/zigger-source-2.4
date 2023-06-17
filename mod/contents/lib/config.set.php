<?php
use Corelib\Func;

////////////////////////////////////////////////////
//
// Module 옵션
//
////////////////////////////////////////////////////

$MODULE_CONTENTS_CONF = array(
    'dir' => 'contents', // 모듈 식별값 (모듈 디렉토리명)
    'title' => '콘텐츠 모듈' // 모듈 명칭
);

////////////////////////////////////////////////////
//
// Module 상수
//
////////////////////////////////////////////////////

define('MOD_CONTENTS', $MODULE_CONTENTS_CONF['dir']); // Module 명칭
define('MOD_CONTENTS_DIR', PH_MOD_DIR.'/'.$MODULE_CONTENTS_CONF['dir']); // Module 경로
define('MOD_CONTENTS_PATH', PH_MOD_PATH.'/'.$MODULE_CONTENTS_CONF['dir']); // Module PHP 경로
define('MOD_CONTENTS_THEME_DIR', PH_THEME_DIR.'/mod-'.$MODULE_CONTENTS_CONF['dir']); // Module Theme PHP 경로
define('MOD_CONTENTS_THEME_PATH', PH_THEME_PATH.'/mod-'.$MODULE_CONTENTS_CONF['dir']); // Module Theme PHP 경로
Func::define_javascript('MOD_CONTENTS_DIR', MOD_CONTENTS_DIR);
