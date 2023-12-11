<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Session;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Mail;
use Make\Library\Sms;

//
// Controller for display
// https://{domain}/sign/signin
//
class Signin extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head('type2');
        $this->layout()->view(PH_THEME_PATH.'/html/sign/signin.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {
        global $CONF;

        $req = Method::request('get', 'redirect');

        if (IS_MEMBER) Func::err_location(SET_ALRAUTH_MSG, PH_DOMAIN);

        $id_val = '';
        $save_checked = '';

        if (isset($_COOKIE['MB_SAVE_ID']) && $_COOKIE['MB_SAVE_ID'] != '') {
            $id_val = $_COOKIE['MB_SAVE_ID'];
            $save_checked = 'checked';
        }

        $this->set('redirect', $req['redirect']);
        $this->set('id_val', $id_val);
        $this->set('save_checked', $save_checked);
        $this->set('show_sns_ka', $CONF['use_sns_ka']);
        $this->set('show_sns_nv', $CONF['use_sns_nv']);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', PH_DIR.'/sign/signin-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Signin )
//
class Signin_submit {

    public function init()
    {
        global $CONF;

        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id, pwd, save, redirect');

        if (IS_MEMBER) Valid::error('', SET_ALRAUTH_MSG);

        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
                'check' => array(
                    'defined' => 'id'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pwd',
                'value' => $req['pwd'],
                'check' => array(
                    'defined' => 'password'
                )
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where mb_id=:col1 and mb_dregdate is null and mb_pwd={$sql->set_password($req['pwd'])}
            ",
            array(
                $req['id']
            )
        );

        if ($sql->getcount() < 1) Valid::error('id', '아이디 혹은 비밀번호가 잘못 되었습니다.');

        // 이메일 인증이 완료되지 않은 아이디인 경우 이메일 인증 화면으로 이동
        if ($sql->fetch('mb_email_chk') == 'N' && $CONF['use_emailchk'] == 'Y') {
            Valid::set(
                array(
                    'return' => 'alert->location',
                    'msg' => '이메일 인증이 완료되지 않은 아이디입니다.',
                    'location' => PH_DIR.'/sign/retry-emailchk?mb_idx='.$sql->fetch('mb_idx')
                )
            );
            Valid::turn();
        }

        $mbinfo = array(
            'id' => $sql->fetch('mb_id'),
            'idx' => $sql->fetch('mb_idx'),
            'remote_addr' => (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
        );

        // 로그인 session 처리
        Session::set_sess('MB_IDX', $mbinfo['idx']);

        // 최근 로그인 내역 기록
        $sql->query(
            "
            update {$sql->table("member")}
            set mb_lately_ip=:col2, mb_lately=now()
            where mb_idx=:col1
            ",
            array(
                $mbinfo['idx'],
                $mbinfo['remote_addr']
            )
        );

        // 아이디 저장을 체크한 경우 아이디를 쿠키에 저장
        if ($req['save'] == 'checked') {
            setcookie('MB_SAVE_ID', $mbinfo['id'], time() + SET_COOKIE_LIFE, '/');

        } else {
            setcookie('MB_SAVE_ID', '', 0, '/');
        }

        // return
        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => urldecode(($req['redirect']) ? $req['redirect'] : PH_DOMAIN)
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/sign/signout
//
class Signout extends \Controller\Make_Controller {

    public function init()
    {
        Method::security('referer');

        if (!IS_MEMBER) Func::err_location(SET_NOAUTH_MSG, PH_DOMAIN);

        // 로그인 session 삭제
        Session::empty_sess('MB_IDX');

        // 로그아웃 후 페이지 이동
        Func::location(PH_DOMAIN);
    }

}

//
// Controller for display
// https://{domain}/sign/signup
//
class Signup extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head('type2');
        $this->layout()->view(PH_THEME_PATH.'/html/sign/signup.tpl.php');
        $this->layout()->foot();
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', PH_DIR.'/sign/signup-submit');
        $form->run();
    }

    public function make()
    {
        global $CONF;

        if (IS_MEMBER) Func::err_location(SET_ALRAUTH_MSG, PH_DOMAIN);

        $this->set('siteconf', $CONF);
        $this->set('show_sns_ka', $CONF['use_sns_ka']);
        $this->set('show_sns_nv', $CONF['use_sns_nv']);
    }

}

//
// Controller for submit
// ( Signup )
//
class signup_submit {

    public function init()
    {
        global $CONF;

        $mail = new Mail();
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id, email, pwd, pwd2, name, gender, phone, phone_code, telephone, address1, address2, address3, policy, mb_1, mb_2, mb_3, mb_4, mb_5, mb_6, mb_7, mb_8, mb_9, mb_10');

        if (IS_MEMBER) Valid::error('', SET_ALRAUTH_MSG);

        Valid::get(
            array(
                'input' => 'policy',
                'value' => $req['policy'],
                'msg' => '이용약관 및 개인정보처리방침에 동의해야 합니다.',
                'check' => array(
                    'checked' => true
                )
            )
        );
        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
                'check' => array(
                    'defined' => 'id'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'email',
                'value' => $req['email'],
                'check' => array(
                    'defined' => 'email'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pwd',
                'value' => $req['pwd'],
                'check' => array(
                    'defined' => 'password'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'pwd2',
                'value' => $req['pwd2'],
                'check' => array(
                    'defined' => 'password'
                )
            )
        );

        if ($req['pwd'] != $req['pwd2']) Valid::error('pwd2', '비밀번호와 비밀번호확인이 일치하지 않습니다.');

        Valid::get(
            array(
                'input' => 'name',
                'value' => $req['name'],
                'check' => array(
                    'defined' => 'nickname'
                )
            )
        );

        // 휴대전화 번호 검사
        Valid::get(
            array(
                'input' => 'phone',
                'value' => $req['phone'],
                'check' => array(
                    'null' => ($CONF['use_mb_phone'] == 'Y') ? false : true,
                    'defined' => 'phone'
                )
            )
        );

        if ($CONF['use_phonechk'] == 'Y' && $CONF['use_sms'] == 'Y' && $req['phone']) {

            // 중복 확인
            $sql->query(
                "
                select count(*) as total
                from {$sql->table("member")}
                where mb_phone=:col1 and mb_dregdate is null
                ",
                array(
                    $req['phone']
                )
            );
            if ($sql->fetch('total') > 0) Valid::error('phone', '이미 등록된 휴대전화 번호입니다.');

            // 인증여부 확인
            $sql->query(
                "
                select count(*) as total
                from {$sql->table("mbchk")}
                where chk_code=:col1 and chk_mode='pchk' and chk_chk='Y' and chk_dregdate is null
                order by chk_regdate desc
                limit 1
                ",
                array(
                    $req['phone'].':'.$req['phone_code']
                )
            );
            if ($sql->fetch('total') < 1) Valid::error('phone', '인증되지 않은 휴대전화 번호입니다. 휴대전화를 인증해주세요.');

        }

        // 전화번호 검사
        Valid::get(
            array(
                'input' => 'telephone',
                'value' => $req['telephone'],
                'check' => array(
                    'null' => ($CONF['use_mb_telephone'] == 'Y') ? false : true,
                    'defined' => 'phone'
                )
            )
        );

        // 주소 검사
        Valid::get(
            array(
                'input' => 'address1',
                'value' => $req['address1'],
                'check' => array(
                    'null' => ($CONF['use_mb_address'] == 'Y') ? false : true
                )
            )
        );
        Valid::get(
            array(
                'input' => 'address2',
                'value' => $req['address2'],
                'check' => array(
                    'null' => ($CONF['use_mb_address'] == 'Y') ? false : true
                )
            )
        );
        Valid::get(
            array(
                'input' => 'address3',
                'value' => $req['address3'],
                'check' => array(
                    'null' => ($CONF['use_mb_address'] == 'Y') ? false : true
                )
            )
        );

        // 아이디 중복 검사
        $sql->query(
            "
            select count(*) as total
            from {$sql->table("member")}
            where mb_id=:col1 and mb_dregdate is null
            ",
            array(
                $req['id']
            )
        );

        if ($sql->fetch('total') > 0) Valid::error('id', '이미 존재하는 아이디입니다.');

        // 이메일 중복 검사
        $sql->query(
            "
            select count(*) as total
            from {$sql->table("member")}
            where mb_email=:col1 and mb_dregdate is null
            ",
            array(
                $req['email']
            )
        );

        if ($sql->fetch('total') > 0) Valid::error('email', '이미 사용중인 이메일입니다. \'회원정보 찾기\' 페이지에서 로그인 정보를 찾을 수 있습니다.');

        // insert
        $mbchk_var = ($CONF['use_emailchk'] == 'Y') ? 'N' : 'Y';
        $remote_addr = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

        $sql->query(
            "
            insert into {$sql->table("member")}
            (mb_id, mb_email, mb_pwd, mb_name, mb_gender, mb_phone, mb_telephone, mb_address, mb_email_chk, mb_regdate, mb_lately, mb_lately_ip, mb_1, mb_2, mb_3, mb_4, mb_5, mb_6, mb_7, mb_8, mb_9, mb_10, mb_sns_ka, mb_sns_nv, mb_sns_ka_token, mb_sns_nv_token, mb_exp)
            values
            (:col1, :col2, {$sql->set_password($req['pwd'])}, :col3, :col4, :col5, :col6, :col7, :col8, now(), now(), :col9, :col10, :col11, :col12, :col13, :col14, :col15, :col16, :col17, :col18, :col19, :col20, :col21, :col22, :col23, :col24)
            ",
            array(
                $req['id'], $req['email'], $req['name'], $req['gender'], $req['phone'], $req['telephone'], $req['address1'].'|'.$req['address2'].'|'.$req['address3'], $mbchk_var, $remote_addr, $req['mb_1'], $req['mb_2'], $req['mb_3'], $req['mb_4'], $req['mb_5'], $req['mb_6'], $req['mb_7'], $req['mb_8'], $req['mb_9'], $req['mb_10'], '', '', '', '', $sql->etcfd_exp('')
            )
        );

        // 회원 idx를 다시 가져옴
        $sql->query(
            "
            select mb_idx
            from {$sql->table("member")}
            where mb_id=:col1 and mb_pwd={$sql->set_password($req['pwd'])} and mb_dregdate is null
            ",
            array(
                $req['id']
            )
        );
        $mb_idx = $sql->fetch('mb_idx');

        // 이메일 인증 메일 발송
        if ($CONF['use_emailchk'] == 'Y') {

            $chk_code = md5(date('YmdHis').$req['id']);
            $chk_url = PH_DOMAIN.PH_DIR.'/sign/emailchk?chk_code='.$chk_code;
            $mail->set(
                array(
                    'tpl' => 'signup',
                    'to' => array(
                        [
                            'email' => $req['email'],
                            'name' => $req['name']
                        ]
                    ),
                    'subject' => $req['name'].'님, '.$CONF['title'].' 이메일 인증을 해주세요.',
                    'chk_url' => '<a href=\''.$chk_url.'\' target=\'_blank\'>'.$chk_url.'</a>'
                )
            );
            $mail->send();

            $sql->query(
                "
                insert into {$sql->table("mbchk")}
                (mb_idx, chk_code, chk_chk, chk_mode, chk_regdate)
                values
                (:col1, :col2, 'N', 'chk', now())
                ",
                array(
                    $mb_idx,
                    $chk_code
                )
            );

            $succ_msg = '이메일로 발송된 메일을 확인해 주시면 회원가입이 완료됩니다. 가입해 주셔서 감사합니다.';

        } else {
            $succ_msg = '회원가입이 완료되었습니다. 가입해 주셔서 감사합니다.';
        }

        // 관리자 최근 피드에 등록
        Func::add_mng_feed(
            array(
                'from' => '회원가입',
                'msg' => '<strong>'.$req['name'].'</strong>님이 회원가입 했습니다.',
                'link' => '/manage/member/modify?idx='.$mb_idx
            )
        );

        // return
        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => $succ_msg,
                'location' => PH_DOMAIN
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( Signup id validator )
//
class Signup_check_id {

    public function init()
    {
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id');

        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
                'msg' => '올바르게 입력하세요.',
                'check' => array(
                    'defined' => 'id'
                )
            )
        );

        $sql->query(
            "
            select count(*) total
            from {$sql->table("member")}
            where mb_id=:col1 and mb_dregdate is null
            ",
            array(
                $req['id']
            )
        );

        if ($sql->fetch('total') > 0) Valid::error('id', '이미 존재하는 아이디입니다.');

        // return
        Valid::set(
            array(
                'return' => 'ajax-validt',
                'msg' => '사용할 수 있는 아이디입니다.'
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( Signup email validator )
//
class Signup_check_email {

    public function init()
    {
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'email');

        Valid::get(
            array(
                'input' => 'email',
                'value' => $req['email'],
                'msg' => '올바르게 입력하세요.',
                'check' => array(
                    'defined' => 'email'
                )
            )
        );

        $sql->query(
            "
            select count(*) total
            from {$sql->table("member")}
            where mb_email=:col1 and mb_dregdate is null
            ",
            array(
                $req['email']
            )
        );

        if ($sql->fetch('total') > 0) Valid::error('email', '이미 존재하는 이메일입니다.');

        // return
        Valid::set(
            array(
                'return' => 'ajax-validt',
                'msg' => '사용할 수 있는 이메일입니다.'
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( Signup password validator )
//
class Signup_check_password {

    public function init()
    {
        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'pwd');

        Valid::get(
            array(
                'input' => 'pwd',
                'value' => $req['pwd'],
                'msg' => '올바르게 입력하세요.',
                'check' => array(
                    'defined' => 'password'
                )
            )
        );

        // return
        Valid::set(
            array(
                'return' => 'ajax-validt',
                'msg' => '사용할 수 있는 비밀번호입니다.'
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/sign/emailchk
//
class Emailchk extends \Controller\Make_Controller {

    public function init()
    {
        $this->common()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/sign/emailchk.tpl.php');
        $this->common()->foot();
    }

    public function make()
    {
        $sql = new Pdosql();

        Method::security('request_get');
        $req = Method::request('get', 'chk_code');

        $succ_var = true;
        $msg = '';

        if (!isset($req['chk_code']) || trim($req['chk_code']) == '') Func::err_location(ERR_MSG_1, PH_DOMAIN);

        // 인증코드 정보 및 인증코드 생성되어 있는지 확인
        $sql->query(
            "
            select *
            from {$sql->table("mbchk")}
            where chk_code=:col1 and (chk_mode='chk' or chk_mode='chg')
            ",
            array(
                $req['chk_code']
            )
        );

        $mb_idx = $sql->fetch('mb_idx');
        $chk_code = $sql->fetch('chk_code');
        $chk_chk = $sql->fetch('chk_chk');
        $chk_mode = $sql->fetch('chk_mode');

        // 인증코드 검사 및 실패시
        if ($sql->getcount() < 1) {
            $msg = '인증 요청 내역을 확인할 수 없습니다.<br />다시 확인 후 시도해 주세요.';
            $succ_var = false;
        }

        // 만료된 인증코드인 경우
        if ($succ_var === true && $chk_code != $req['chk_code']) {
            $msg = '만료된 인증코드 이거나, 존재하지 않는 인증코드 입니다.<br />인증코드 재발송 후 다시 시도해 주시기 바랍니다.';
            $succ_var = false;
        }

        // 이미 인증된 경우
        if ($succ_var === true && $chk_chk == 'Y') {
            $msg = '이미 이메일 인증을 완료 하였습니다.<br />회원님의 아이디로 홈페이지를 정상적으로 이용할 수 있습니다.';
            $succ_var = false;
        }

        // 인증 성공한 경우
        if ($succ_var === true) {

            // 신규가입 인증인 경우
            if ($chk_mode == 'chk') {
                $sql->query(
                    "
                    update {$sql->table("member")}
                    set mb_email_chk='Y'
                    where mb_idx=:col1
                    ",
                    array(
                        $mb_idx
                    )
                );
            }

            // 이메일 변경 인증인 경우
            if ($chk_mode == 'chg') {
                $sql->query(
                    "
                    update {$sql->table("member")}
                    set mb_email=mb_email_chg, mb_email_chg=''
                    where mb_idx=:col1
                    ",
                    array(
                        $mb_idx
                    )
                );
            }

            // update
            $sql->query(
                "
                update {$sql->table("mbchk")}
                set chk_chk='Y'
                where chk_code=:col1
                ",
                array(
                    $chk_code
                )
            );

            $msg = '회원님의 이메일이 성공적으로 인증되었습니다.<br />로그인 후 정상적으로 서비스 이용 가능합니다.<br />감사합니다.';

        }

        $this->set('msg', $msg);
    }
}

//
// Controller for display
// https://{domain}/sign/retry-emailchk
//
class Retry_emailchk extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/sign/retry_emailchk.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {
        global $CONF;

        $sql = new Pdosql();
        $mail = new Mail();

        $req = Method::request('get', 'mb_idx');
        $p_req = Method::request('post', 'p_mb_idx');

        if (!isset($p_req['p_mb_idx']) && !isset($req['mb_idx'])) Func::err_back(ERR_MSG_1);

        // post parameter가 있는 경우 (submit 된 경우) 인증메일 재발송
        if (isset($p_req['p_mb_idx']) && trim($p_req['p_mb_idx']) != '') {

            $sql->query(
                "
                select *
                from {$sql->table("member")}
                where mb_idx=:col1 and mb_email_chk='N' and mb_dregdate is null
                ",
                array(
                    $p_req['p_mb_idx']
                )
            );

            if ($sql->getcount() < 1) Func::err_back('회원 정보를 찾을 수 없습니다.');

            $mbinfo = $sql->fetchs();

            $chk_code = md5(date('YmdHis').$mbinfo['mb_id']);
            $chk_url = PH_DOMAIN.PH_DIR.'/sign/emailchk?chk_code='.$chk_code;
            $mail->set(
                array(
                    'tpl' => 'signup',
                    'to' => array(
                        [
                            'email' => $mbinfo['mb_email'],
                            'name' => $mbinfo['mb_name']
                        ]
                    ),
                    'subject' => $mbinfo['mb_name'].'님, '.$CONF['title'].' 이메일 인증을 해주세요.',
                    'chk_url' => '<a href="'.$chk_url.'" target"_blank">'.$chk_url.'</a>'
                )
            );
            $mail->send();

            $sql->query(
                "
                insert into {$sql->table("mbchk")}
                (mb_idx, chk_code, chk_chk, chk_mode, chk_regdate)
                values
                (:col1, :col2, 'N', 'chk', now())
                ",
                array(
                    $mbinfo['mb_idx'],
                    $chk_code
                )
            );

            Func::err_location('인증 메일을 성공적으로 재발송 하였습니다.', PH_DOMAIN);
        }

        $this->set('mb_idx', $req['mb_idx']);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'static');
        $form->set('action', PH_DIR.'/sign/retry-emailchk');
        $form->set('target', 'view');
        $form->set('method', 'post');
        $form->run();
    }

}

//
// Controller for display
// https://{domain}/sign/forgot
//
class Forgot extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head('type2');
        $this->layout()->view(PH_THEME_PATH.'/html/sign/forgot.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {
        if (IS_MEMBER) Func::err_location(SET_ALRAUTH_MSG, PH_DOMAIN);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', PH_DIR.'/sign/forgot-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Forgot )
//
class Forgot_submit {

    public function init()
    {
        global $CONF;

        $sql = new Pdosql();
        $mail = new Mail();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'email');

        if (IS_MEMBER) Valid::error('', SET_ALRAUTH_MSG);

        Valid::get(
            array(
                'input' => 'email',
                'value' => $req['email'],
                'check' => array(
                    'defined' => 'email'
                )
            )
        );

        // 회원정보 확인
        $sql->query(
            "
            select *, count(*) as total
            from {$sql->table("member")}
            where mb_email=:col1 and mb_dregdate is null
            order by mb_regdate desc
            limit 1
            ",
            array(
                $req['email']
            )
        );

        if ($sql->fetch('total') < 1) Valid::error('email', '회원 정보를 찾을 수 없습니다. 이메일 주소를 확인해 주세요.');

        $mb_id = $sql->fetch('mb_id');
        $mb_name = $sql->fetch('mb_name');

        // 임시 비밀번호 생성 및 정보 update
        $upw = substr(md5(date('YmdHis').$mb_id), 0, 10);

        $sql->query(
            "
            update {$sql->table("member")}
            set mb_pwd={$sql->set_password($upw)}
            where mb_id=:col1 and mb_dregdate is null
            ",
            array(
                $mb_id
            )
        );

        // 회원 메일로 임시 비밀번호 발송
        $mail->set(
            array(
                'tpl' => 'forgot',
                'to' => array(
                    [
                        'email' => $req['email'],
                        'name' => $mb_name
                    ]
                ),
                'subject' => $mb_name.'님의 '.$CONF['title'].' 로그인 정보입니다.',
                'mb_id' => $mb_id,
                'mb_pwd' => $upw
            )
        );
        $mail->send();

        // return
        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '회원님의 이메일로 로그인 정보가 성공적으로 발송 되었습니다.',
                'location' => PH_DOMAIN
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( 회원 휴대전화번호 중복체크 및 등록 )
//
class Phonechk_submit {

    public function init()
    {
        global $CONF;

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'phone');

        $sql = new Pdosql();
        $sms = new Sms();

        Valid::get(
            array(
                'input' => 'phone',
                'value' => $req['phone'],
                'check' => array(
                    'null' => false,
                    'defined' => 'phone'
                )
            )
        );

        // 다른 회원이 사용중인 휴대전화 번호인지 검사
        $sql->query(
            "
            select count(*) as total
            from {$sql->table("member")}
            where mb_phone=:col1 and mb_dregdate is null
            ",
            array(
                $req['phone']
            )
        );
        if ($sql->fetch('total') > 0) Valid::error('phone', '이미 등록된 휴대전화 번호입니다.');

        // 코드 생성
        $code = rand(100000, 999999);

        // insert
        $sql->query(
            "
            insert into {$sql->table("mbchk")}
            (mb_idx, chk_code, chk_mode, chk_chk, chk_regdate)
            values
            (:col1, :col2, :col3, :col4, now())
            ",
            array(
                0, $req['phone'].':'.$code, 'pchk', 'N'
            )
        );

        // 코드 SMS 발송
        $sms->set(
            array(
                'to' => [
                    $req['phone']
                ],
                'memo' => $CONF['title'].' - 인증코드 ['.$code.'] 를 입력해주세요.'
            )
        );
        $sms->send();

        // 코드 발송 완료
        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'Get_phonecheck_beforeConfirm()'
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( SMS 휴대전화 본인인증 수행 )
//
class phonechk_confirm_submit {

    public function init()
    {
        global $CONF;

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'phone, phone_code');

        $sql = new Pdosql();

        // 코드 검증
        $sql->query(
            "
            select count(*) as total
            from {$sql->table("mbchk")}
            where chk_code=:col1 and chk_mode='pchk' and chk_dregdate is null
            ",
            array(
                $req['phone'].':'.$req['phone_code']
            )
        );

        if ($sql->fetch('total') < 1) Valid::error('phone_code', '인증코드가 올바르지 않습니다.');

        // 코드 인증 처리
        $sql->query(
            "
            update {$sql->table("mbchk")}
            set chk_chk='Y'
            where chk_code=:col1 and chk_mode='pchk' and chk_dregdate is null
            ",
            array(
                $req['phone'].':'.$req['phone_code']
            )
        );

        // 코드 검증 완료
        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'Get_phonecheck_afterConfirm()'
            )
        );
        Valid::turn();
    }

}
