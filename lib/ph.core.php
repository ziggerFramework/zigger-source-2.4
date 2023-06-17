<?php
$REAL_PATH = str_replace('\\', '/' , str_replace(basename(__FILE__), '', realpath(__FILE__)));
$REAL_PATH = str_replace('/lib/', '', $REAL_PATH);
define('REAL_PATH', $REAL_PATH);
define('REAL_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', REAL_PATH));

if(!file_exists(REAL_PATH.'/data/dbconn.set.php')){
    echo '<script type="text/javascript">location.href=\''.REAL_DIR.'/install/\';</script>';
    exit;
}

// zigger libraries
require_once REAL_PATH.'/data/path.set.php';
require_once REAL_PATH.'/data/dbconn.set.php';
require_once REAL_PATH.'/lib/config.set.php';
require_once PH_PATH.'/lib/autoload.class.php';
require_once PH_PATH.'/lib/pdo.class.php';
require_once PH_PATH.'/lib/functions.class.php';
require_once PH_PATH.'/lib/session.class.php';
require_once PH_PATH.'/lib/paging.class.php';
require_once PH_PATH.'/lib/uploader.class.php';
require_once PH_PATH.'/lib/imgresize.class.php';
require_once PH_PATH.'/lib/mail.class.php';
require_once PH_PATH.'/lib/sms.class.php';
require_once PH_PATH.'/lib/method.class.php';
require_once PH_PATH.'/lib/valid.class.php';
require_once PH_PATH.'/lib/controller.class.php';
require_once PH_PATH.'/lib/layoutfunc.class.php';
require_once PH_PATH.'/lib/variable.inc.php';
require_once PH_PATH.'/lib/statistic.class.php';
require_once PH_PATH.'/lib/blocked.class.php';
require_once PH_MANAGE_PATH.'/lib/functions.class.php';
require_once PH_PLUGIN_PATH.'/aws/aws-autoloader.php';

// 모듈별 기본 설정 파일
foreach ($MODULE as $key => $val) {

    $file = PH_MOD_PATH.'/'.$val.'/lib/config.set.php';
    if (file_exists($file)) require_once $file;

    $file = PH_MOD_PATH.'/'.$val.'/lib/lib.inc.php';
    if (file_exists($file)) require_once $file;

    $file = PH_MOD_PATH.'/'.$val.'/lib/controller.class.php';
    if (file_exists($file)) require_once $file;

    $file = PH_MOD_PATH.'/'.$val.'/lib/functions.class.php';
    if (file_exists($file)) require_once $file;

}
