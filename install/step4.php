<?php
use Corelib\Method;
use Corelib\Func;

require_once './install.core.php';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$realdir = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace("\\", '/', str_replace(basename(__FILE__), '', realpath(__FILE__))));
$realdir = str_replace('/install/', '', $realdir);

$req = Method::request('post', 'engine, host, name, user, pwd, pfx');

if (step1_chk() === false) Func::err_location('Step 1 부터 진행해야 합니다.', './index.php');

if (!file_exists('../data/dbconn.temp.php')) {

    if (!$req['engine'] || !$req['host'] || !$req['name'] || !$req['user'] || !$req['pwd'] || !$req['pfx']) Func::err_location('Database 정보가 입력되지 않았습니다.', './index.php');
    if (!preg_match('/^[0-9a-zA-Z_]+$/', $req['pfx'])) Func::err_location('Table Prefix가 올바르지 않습니다.', './index.php');

    try {
        switch ($req['engine']) {
            case 'mysql' :
                $pdo = new \PDO(
                    'mysql:host='.$req['host'].';dbname='.$req['name'], $req['user'], $req['pwd'],
                    array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                    )
                );

                break;
        }
    }
    catch (Exception $e) {
        Func::err_location('Database 접속에 실패했습니다.', './index.php');
    }

    $stmt = $pdo->query("
        select count(*) as has_table from Information_schema.tables where table_schema='{$req['name']}' and table_name='{$req['pfx']}member';
    ");
    $stmt->execute();
    if ($stmt->fetch()['has_table'] != 0) Func::err_location('이미 Database Table이 생성되었습니다. Table삭제 후 재설치 바랍니다.', './index.php');

    include_once './scheme/core.'.$req['engine'].'.sql';
    include_once './scheme/modules.'.$req['engine'].'.sql';

    $stmt = $pdo->prepare($SCHEME_CORE);
    $stmt->execute();
    $stmt = $pdo->prepare($SCHEME_MOD);
    $stmt->execute();

    $file = @fopen('../data/dbconn.temp.php', 'w');
    fwrite($file,"<?php\ndefine('DB_ENGINE', '".$req['engine']."');\ndefine('DB_HOST', '".$req['host']."');\ndefine('DB_USER', '".$req['user']."');\ndefine('DB_PWD', '".$req['pwd']."');\ndefine('DB_NAME', '".$req['name']."');\ndefine('DB_PREFIX', '".$req['pfx']."');\ndefine('DB_SPECIALCHARS', 1);\ndefine('DB_NL2BR', 1);\n?>");
    fclose($file);

    $file = @fopen('../data/path.set.php', 'w');
    fwrite($file,"<?php\ndefine('PH_DOMAIN', '".$protocol.$_SERVER['HTTP_HOST'].$realdir."');\ndefine('PH_DIR', '".$realdir."');\ndefine('PH_PATH', \$_SERVER['DOCUMENT_ROOT'].PH_DIR);\n?>");
    fclose($file);
}

require_once './head.set.php';
require_once './html/step4.html';
require_once './foot.set.php';
