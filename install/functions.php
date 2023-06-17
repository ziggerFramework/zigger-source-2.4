<?php
function permschk($dir)
{
    return is_writable($dir);
}

function phpversions()
{
    $version = (float)phpversion();
    return ($version > '5.5') ? true : false;
}

function extschk($exts)
{
    $loaded = extension_loaded($exts);
    return ($loaded !== false) ? true : false;
}

function step1_chk()
{
    $loaded = array(
        permschk('../data/'),
        permschk('../robots.txt'),
        phpversions(),
        extschk('GD'),
        extschk('mbstring'),
        extschk('PDO'),
        extschk('curl')
    );

    foreach ($loaded as $key => $value) {
        if ($value !== true) return false;
    }

    return true;
}
