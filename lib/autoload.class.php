<?php
// autoloader
function mod_autoloader($className)
{
    if (strpos($className, 'Module\\') === false) {
        return;
    }

    $className = strtolower($className);

    $className = str_replace(
        array('module', '\\'),
        array('', '/'),
        $className
    );

    $file = basename($className);
    $loadfile = preg_replace("/($file(?!.*$file))/", 'controller/'.$file, $className);
    $loadfile = PH_MOD_PATH.$loadfile.'.php';

    if (file_exists($loadfile) === true) {
        include_once $loadfile;
    }
}
