<?php
use Corelib\Func;

require_once '../lib/pdo.class.php';
require_once '../lib/functions.class.php';
require_once '../lib/method.class.php';
require_once '../lib/valid.class.php';
require_once './functions.php';

if (file_exists('../data/dbconn.set.php')) Func::location('../');
