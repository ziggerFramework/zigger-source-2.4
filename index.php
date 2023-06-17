<?php

////////////////////////////////////////////////////
//
//  Zigger Router
//
//  Received parameter from '.htaccess' file.
//  (rewritepage, rewritetype)
//
////////////////////////////////////////////////////

use Corelib\Func;
use Corelib\Method;

require_once './lib/ph.core.php';

// rewrite rule을 통해 전달된 get parameter 처리
$REQUEST = Method::request('get', 'rewritepage, rewritetype');

if (!isset($rewritepage) && isset($REQUEST['rewritepage'])) {
    $rewritepage = $REQUEST['rewritepage'];

} else if (!isset($rewritepage) || !$rewritepage) {
    $rewritepage = 'index';
}

// rewritepage 처리
$rewritepage = str_replace('.php', '', $rewritepage);
$REL_HREF = explode('/', $rewritepage);

$REL_PATH = array('page_name' => 'index', 'class_name' => 'index', 'full_path' => '', 'first_path' => '', 'namespace' => '');

if (count($REL_HREF) > 1) {
    $REL_PATH['page_name'] = $REL_HREF[count($REL_HREF) - 2];
    $REL_PATH['class_name'] = $REL_HREF[count($REL_HREF) - 1];
    $REL_PATH['full_path'] = str_replace('/'.$REL_PATH['page_name'].'/'.$REL_PATH['class_name'], '', '/'.$rewritepage);
    $REL_PATH['first_path'] = $REL_HREF[0];
    $REL_PATH['namespace'] = '';

} else if ($rewritepage != 'index') {
    $REL_PATH['page_name'] = $REL_HREF[0];
    $REL_PATH['class_name'] = 'Index';
}

// controller path 선언
$root = PH_PATH;
$root_dir = opendir($root);
$root_index = array();

while ($dir = readdir($root_dir)) {
    $except = array('.', '..', 'LICENSE', 'README', 'app', '.htaccess', 'robots.txt', 'index.php', 'install');
    if (in_array($dir, $except) !== true) $root_index[$dir] = $dir;
}

$includeFile = PH_PATH.$REL_PATH['full_path'].'/'.$REL_PATH['page_name'].'.php';

if (in_array($REL_PATH['first_path'], $root_index) === false) {
    $includeFile = PH_PATH.'/app'.$REL_PATH['full_path'].'/'.$REL_PATH['page_name'].'.php';
}

if (strpos($rewritepage, 'manage/mod/') !== false) {
    $relEx = explode('/', $REL_PATH['full_path']);
    $includeFile = PH_MOD_PATH.'/'.$relEx[count($relEx) - 1].'/manage.set/'.$REL_PATH['page_name'].'.php';
}

if ($REL_PATH['first_path'] == 'mod') {
    $moduleNameEx = explode('/', str_replace(PH_PATH.'/mod/', '', $includeFile));
    $REL_PATH['namespace'] = 'Module\\'.$moduleNameEx[0].'\\';
}

// class name 선언
$class_name = ucfirst($REL_PATH['class_name']);
$class_name = str_replace(array('-', '.'), array('_', '_'), $class_name);
$class_name = $REL_PATH['namespace'].$class_name;

// controller include 및 비정상 접근 error 처리
$is_submit_action = (isset($REQUEST['rewritetype']) && $REQUEST['rewritetype'] == 'submit') ? true : false;

if (file_exists($includeFile) === false) ($is_submit_action === true) ? Func::core_err(ERR_MSG_14) : Func::location(PH_DIR.'/error/code404');
require_once $includeFile;

// class 호출
if (class_exists($class_name) === false) ($is_submit_action === true) ? Func::core_err(ERR_MSG_15) : Func::location(PH_DIR.'/error/code404');

// method 호출
$$class_name = new $class_name();
if (method_exists($$class_name, 'func') !== false) $$class_name->func();
$$class_name->init();
