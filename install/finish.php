<?php
use Corelib\Method;
use Corelib\Func;

require_once './install.core.php';
require_once '../data/dbconn.temp.php';

ob_start();
mb_internal_encoding('UTF-8');

$req = Method::request('post', 'name, id, pwd, pwd2');

if (!file_exists('../data/dbconn.temp.php')) Func::err_location('DB 설정이 진행되지 않았습니다.', './index.php');
if (step1_chk() === false) Func::err_location('Step 1 부터 진행해야 합니다.', './index.php');
if (!$req['name'] || !$req['id'] || !$req['pwd'] || !$req['pwd2']) Func::err_back('관리자 정보가 입력되지 않았습니다.');

$id = trim($req['name']);
if (!preg_match("/^[가-힣0-9a-zA-Z]+$/", $req['name']) || mb_strlen($id) < 2 || mb_strlen($id) > 12)Func::err_back('ID가 올바르지 않습니다.');

$id = trim($req['id']);
if (!preg_match("/^[0-9a-z]+$/", $req['id']) || mb_strlen($id) < 5 || mb_strlen($id) > 30) Func::err_back('ID가 올바르지 않습니다.');

$pwd = trim($req['pwd']);
if (mb_strlen($pwd) < 5 || mb_strlen($pwd) > 50) Func::err_back('Password가 올바르지 않습니다.');

if ($req['pwd'] != $req['pwd2']) Func::err_back('Password와 Password확인이 일치하지 않습니다.');

try {
    switch (DB_ENGINE) {
        case 'mysql' :
            $pdo = new \PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PWD,
                array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                )
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            break;
    }
}
catch (Exception $e) {
    Func::err_back('Database 접속에 실패했습니다.');
}

require_once './scheme/insertadm.'.DB_ENGINE.'.sql';

$stmt = $pdo->prepare($SCHEME_INSERTADM);
$stmt->execute();

rename('../data/dbconn.temp.php', '../data/dbconn.set.php');

require_once './head.set.php';
require_once './html/finish.html';
require_once './foot.set.php';
