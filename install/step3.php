<?php
use Corelib\Method;
use Corelib\Func;

require_once './install.core.php';

$req = Method::request('post', 'lgpl_agree');

if (step1_chk() === false) Func::err_location('Step 1 부터 진행해야 합니다.', './index.php');
if ($req['lgpl_agree'] != 'checked') Func::err_back('약관에 동의해야 다음 단계 진행이 가능합니다.');

require_once './head.set.php';
require_once './html/step3.html';
require_once './foot.set.php';
