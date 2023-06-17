<?php
if ($type != '') $type = '.'.$type;
require_once PH_PATH.'/layout/head.set.php';
require_once PH_PATH.'/layout/print.sitemap.php';
require_once PH_THEME_PATH.'/layout/head'.$type.'.tpl.php';
