<?php
use Corelib\Method;
use Corelib\Func;
use Make\Database\Pdosql;

include_once '../lib/ph.core.php';

$sql = new Pdosql();

Method::security('request_get');
$req = Method::request('get', 'idx, key');

if (!$req['idx'] || !$req['key']) Func::location(PH_DOMAIN);

$sql->query(
    "
    select link
    from {$sql->table("banner")}
    where idx=:col1 and bn_key=:col2
    ",
    array(
        $req['idx'], $req['key']
    )
);

if ($sql->getcount() < 1) Func::location(PH_DOMAIN.PH_DIR);

$link = $sql->fetch('link');

$sql->query(
    "
    update
    {$sql->table("banner")}
    set hit=hit+1
    where idx=:col1 and bn_key=:col2
    ",
    array(
        $req['idx'], $req['key']
    )
);

Func::location($link);
