<?php
use Corelib\Session;
use Corelib\Func;
use Make\Database\Pdosql;

$varsql = new Pdosql();

// modules
$mpath = PH_MOD_PATH;
$mopen = opendir($mpath);
$midx = 0;

while ($dir = readdir($mopen)) {
    if ($dir != '.' && $dir != '..' && !preg_match('/^\./', $dir)) {
        $MODULE[$midx] = $dir;
        $midx++;
    }
}
sort($MODULE);

// themes
$tpath = PH_PATH.'/theme/';
$topen = opendir($tpath);
$tidx = 0;

while ($dir = readdir($topen)) {
    if ($dir != '.' && $dir != '..' && !preg_match('/^\./', $dir)) {
        $THEME[$tidx] = $dir;
        $tidx++;
    }
}

// default information
$CONF = array();

$varsql->query(
    "
    select *
    from {$varsql->table("config")}
    where cfg_type='engine'
    ", []
);

if ($varsql->getcount() > 0) {
    do {
        $cfg = $varsql->fetchs();
        $CONF[$cfg['cfg_key']] = $cfg['cfg_value'];

        $varsql->specialchars = 1;
        $varsql->nl2br = 1;

        if (in_array($cfg['cfg_key'], array('script', 'meta'))) {
            $varsql->specialchars = 0;
            $varsql->nl2br = 0;

            $CONF[$cfg['cfg_key']] = $varsql->fetch('cfg_value');
        }

    } while($varsql->nextRec());
}

// default images & icons
$icons = array(
    'favicon', 'logo', 'og_image'
);

foreach ($icons as $key => $value) {
    if ($CONF[$value]) {
        $icon = Func::get_fileinfo($CONF[$value], false);

        if ($icon['storage'] == 'Y') {
            $CONF[$value] = $CONF['s3_key1'].'/'.$CONF['s3_key2'].'/manage/'.$icon['repfile'];

        } else {
            $CONF[$value] = PH_DATA_DIR.'/manage/'.$icon['repfile'];
        }
    }
}

// theme constants
define('PH_THEME', $CONF['theme']); // theme 경로
define('PH_THEME_DIR', PH_DIR.'/theme/'.$CONF['theme']); // theme 경로
define('PH_THEME_PATH', PH_PATH.'/theme/'.$CONF['theme']); // theme PHP 경로

// 회원이라면, 회원의 기본 정보 가져옴
define('IS_MEMBER', Session::is_sess('MB_IDX'));
define('MB_IDX', (IS_MEMBER) ? Session::sess('MB_IDX') : null);

$MB = array();

if (IS_MEMBER) {
    $varsql->query(
        "
        select *
        from {$varsql->table("member")}
        where mb_idx=:col1
        ",
        array(
            MB_IDX
        )
    );

    $mb_arr = $varsql->fetchs();

    foreach ($mb_arr as $key => $value) {
        $key = str_replace('mb_', '', $key);
        $MB[$key] = $value;
    }

    for ($i = 1; $i <= 10; $i++) {
        $MB['mb_'.$i] = $MB[$i];
        unset($MB[$i]);
    }

} else {
    $MB = array(
        'level' => 10,
        'adm' => null,
        'idx' => 0,
        'id' => null,
        'pwd' => null,
        'email' => null,
        'name' => null,
        'phone' => null,
        'telephone' => null
    );
}

// 회원 레벨별 명칭 배열화
$MB['type'] = array();
$vars = explode('|', $CONF['mb_division']);

for ($i = 1; $i <= 10; $i++) {
    $MB['type'][$i] = $vars[$i - 1];
}

// 회원 정보 필수값 확인
if (IS_MEMBER && $MB['level'] > 1 && !strstr(Func::thisuri(), '/member/') && !strstr(Func::thisuri(), '/sign/') && !strstr(Func::thisuri(), '/manage/')) {
    if ($CONF['use_mb_phone'] == 'Y' && !$MB['phone']) $field = '휴대전화번호';
    if ($CONF['use_mb_telephone'] == 'Y' && !$MB['telephone']) $field = '전화번호';
    if ($CONF['use_mb_address'] == 'Y' && $MB['address'] == '||') $field = '주소';
    if (isset($field)) Func::err_location('회원정보에 필수 정보('.$field.')가 누락되어 회원관리 페이지로 이동합니다.\n정보 등록후 이용해주세요.', PH_DOMAIN.'/member/info');
}

// php_ini의 post_max_size 값 확인
$CONF['ini_post_max_size'] = Func::ini_post_max_size();

// 업데이트 초기화 확인
Func::chk_update_config_field(
    array(
        'use_sms', 'use_feedsms', 'sms_toadm', 'sms_from', 'sms_key1', 'sms_key2', 'sms_key3', 'sms_key4', //ver 2.2.1
        'use_mb_phone:N', 'use_phonechk:N', 'use_mb_telephone:N', 'use_mb_address:N', 'use_mb_gender:N', //ver 2.2.2
        'use_rss:N', 'rss_boards', //ver 2.3.3
        's3_path_style:N' //ver 2.3.7
    )
);
