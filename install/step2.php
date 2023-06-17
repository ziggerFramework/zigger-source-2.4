<?php
use Corelib\Func;

require_once './install.core.php';
require_once './head.set.php';

if (step1_chk() === false) Func::err_location('Step 1 부터 진행해야 합니다.', './index.php');

require_once './html/step2.html';
require_once './foot.set.php';
