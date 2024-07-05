<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Session;
use Corelib\Valid;
use Corelib\Blocked;
use Make\Database\Pdosql;
use Make\Library\Mail;
use Make\Library\Paging;
use Make\Library\Uploader;
use Module\Message\Library as Message_Library;
use Module\Alarm\Library as Alarm_Library;

//
// Controller for display
// https://{domain}/member
//
class Index extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/member/index.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {
        global $MB;

        $Message_Library = new Message_Library();
        $Alarm_Library = new Alarm_Library();

        Func::getlogin(SET_NOAUTH_MSG);

        $this->set('message_new_count', Func::number($Message_Library->get_new_count()));
        $this->set('alarm_new_count', Func::number($Alarm_Library->get_new_count()));
        $this->set('point_total_count', Func::number($MB['point']));
    }

}

//
// Controller for display
// https://{domain}/member/info
//
class Info extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/member/info.tpl.php');
        $this->layout()->foot();
    }

    public function func()
    {
        // 성별 처리
        function gender_chked($obj)
        {
            $arr = array('M' => '', 'F' => '');

            foreach ($arr as $key => $value) {
                if ($key == $obj['mb_gender']) $arr[$key] = 'checked';
            }

            return $arr;
        }
    }

    public function make()
    {
        global $CONF, $MB;

        $sql = new Pdosql();

        Func::getlogin(SET_NOAUTH_MSG);

        if ($MB['adm'] == 'Y') Func::err_location('최고 레벨의 관리자는 Manage 에서 정보 변경 가능합니다.', PH_DOMAIN);

        // 회원 정보 select
        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where `mb_idx`=:col1 and `mb_dregdate` is null
            ",
            array(
                MB_IDX
            )
        );
        $arr = $sql->fetchs();

        $arr['mb_point'] = Func::number($arr['mb_point']);
        $arr['mb_regdate'] = Func::datetime($arr['mb_regdate']);
        $arr['mb_lately'] = Func::datetime($arr['mb_lately']);

        // 주소 처리
        $arr[0]['mb_address'] = explode('|', $arr['mb_address']);
        if (!isset($arr[0]['mb_address'][0])) $arr[0]['mb_address'][0] = '';
        if (!isset($arr[0]['mb_address'][1])) $arr[0]['mb_address'][1] = '';
        if (!isset($arr[0]['mb_address'][2])) $arr[0]['mb_address'][2] = '';

        // 프로필 이미지 처리
        $arr[0]['mb_profileimg'] = '';
        if ($arr['mb_profileimg']) {
            $fileinfo = Func::get_fileinfo($arr['mb_profileimg']);
            $arr[0]['mb_profileimg'] = $fileinfo['replink'];
        }

        // 변수 재정의
        $mb = array();

        if (isset($arr)) {
            foreach($arr as $key => $value){
                $mb[$key] = $value;
            }

        }else{
            $mb = null;
        }

        $this->set('siteconf', $CONF);
        $this->set('gender_chked', gender_chked($arr));
        $this->set('mb', $mb);
        $this->set('max_pfimg_size', Func::getbyte(SET_MAX_PFIMG_UPLOAD));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'multipart');
        $form->set('action', PH_DIR.'/member/info-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Info )
//
class Info_submit {

    public function init()
    {
        global $req, $file;

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, email, pwd, pwd2, name, gender, phone_chg, phone, phone_code, telephone, address1, address2, address3, email_chg_cc');
        $file = Method::request('file', 'profileimg');

        if (!IS_MEMBER) Valid::error('', SET_NOAUTH_MSG);

        switch ($req['mode']) {
            case 'mdf' :
                $this->get_mdf();
                break;

            case 'lv' :
                $this->get_lv();
                break;

            default :
                Valid::error('', ERR_MSG_9);
        }
    }

    //
    // 회원 정보 변경
    //
    private function get_mdf()
    {
        global $CONF, $MB, $req, $file;

        $sql = new Pdosql();
        $mail = new Mail();
        $uploader = new Uploader();

        Valid::get(
            array(
                'input' => 'name',
                'value' => $req['name'],
                'check' => array(
                    'defined' => 'nickname'
                )
            )
        );

        // 기존 회원 정보에 이메일이 없다면 수정 금지
        if (!$req['email'] && $req['email_chg_cc'] != 'checked' && !$MB['email']) Valid::error('email', '기존 회원정보에 이메일 정보가 없어 수정이 불가합니다. 수정 전 이메일 변경 먼저 해주세요.');

        // 비밀번호가 입력된 경우
        if ($req['pwd'] != $req['pwd2']) Valid::error('pwd2', '비밀번호와 비밀번호 확인이 일치하지 않습니다.');

        if ($req['pwd'] != '') {
            Valid::get(
                array(
                    'input' => 'pwd',
                    'value' => $req['pwd'],
                    'check' => array(
                        'defined' => 'password'
                    )
                )
            );
        }

        // 이메일이 입력된 경우
        $mb_email_chg = $MB['email_chg'];

        if ($req['email'] != '' && $req['email'] == $MB['email']) Valid::error('email', '회원님이 이미 사용중인 이메일입니다.');

        if ($req['email'] != '') {
            Valid::get(
                array(
                    'input' => 'email',
                    'value' => $req['email'],
                    'check' => array(
                        'defined' => 'email'
                    )
                )
            );

            $sql->query(
                "
                select count(*) as total
                from {$sql->table("member")}
                where `mb_email`=:col1 and `mb_email`!=:col2 and `mb_dregdate` is null
                ",
                array(
                    $req['email'],
                    $MB['email']
                )
            );

            if ($sql->fetch('total') > 0) Valid::error('email', '다른 회원이 사용중인 이메일입니다.');
            $mb_email_chg = $req['email'];
        }

        // 이메일이 입력된 경우 인증 메일 발송
        if ($req['email'] != '') {
            $chk_code = md5(date('YmdHis').$req['email']);
            $chk_url = PH_DOMAIN.'/sign/emailchk?chk_code='.$chk_code;

            $mail->set(
                array(
                    'tpl' => 'signup',
                    'to' => array(
                        [
                            'email' => $req['email'],
                            'name' => $req['name']
                        ]
                    ),
                    'subject' => $req['name'].'님, '.$CONF['title'].' 이메일 변경 인증을 해주세요.',
                    'chk_url' => '<a href="'.$chk_url.'" target="_blank">[이메일 인증하기]</a>'
                )
            );
            $mail->send();

            $sql->query(
                "
                insert into {$sql->table("mbchk")}
                (`mb_idx`, `chk_code`, `chk_chk`, `chk_mode`, `chk_regdate`)
                values
                (:col1, :col2, 'N', 'chg', now())
                ",
                array(
                    MB_IDX,
                    $chk_code
                )
            );
        }

        // 이메일 변경 취소
        if (!$req['email'] && $req['email_chg_cc'] == 'checked') $mb_email_chg = '';

        // 휴대전화 번호 검사
        $mb_phone = $req['phone'];

        if ($MB['phone'] && !$req['phone'] && $CONF['use_phonechk'] == 'Y') $mb_phone = $MB['phone'];

        Valid::get(
            array(
                'input' => 'phone',
                'value' => $mb_phone,
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
                where `mb_phone`=:col1 and `mb_dregdate` is null
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
                where `chk_code`=:col1 and `chk_mode`='pchk' and `chk_chk`='Y' and `chk_dregdate` is null
                order by `chk_regdate` desc
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

        // 프로필 이미지 처리
        $uploader->path = PH_DATA_PATH.'/memberprofile';
        $uploader->chkpath();

        $profileimg_name = '';

        if (isset($file['profileimg'])) {
            $uploader->file = $file['profileimg'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('profileimg', '허용되지 않는 프로필 이미지 유형입니다.');
            if ($uploader->chkbyte(SET_MAX_PFIMG_UPLOAD) !== true) Valid::error('profileimg', '프로필 이미지 용량이 허용 용량을 초과합니다.');
            $profileimg_name = $uploader->replace_filename($file['profileimg']['name']);
            if (!$uploader->upload($profileimg_name)) Valid::error('profileimg', '프로필 이미지 업로드 실패');
        }

        if ((isset($file['profileimg']) && $MB['profileimg'] != '')) $uploader->drop($MB['profileimg']);
        if ($MB['profileimg'] != '' && !isset($file['profileimg'])) $profileimg_name = $MB['profileimg'];

        // 이름 중복 검사
        if ($CONF['use_allow_dup_name'] == 'Y') {
            $sql->query(
                "
                select count(*) total
                from {$sql->table("member")}
                where `mb_name`=:col1 and `mb_dregdate` is null and `mb_idx`!=:col2
                ",
                array(
                    $req['name'], $MB['idx']
                )
            );
    
            if ($sql->fetch('total') > 0) Valid::error('name', '이미 존재하거나, 사용할 수 없는 이름입니다.');
        }

        // update
        if ($req['pwd'] != '') {
            $sql->query(
                "
                update {$sql->table("member")}
                set `mb_pwd`={$sql->set_password($req['pwd'])}, `mb_name`=:col2, `mb_gender`=:col3, `mb_phone`=:col4, `mb_address`=:col5, `mb_telephone`=:col6, `mb_email_chg`=:col7, `mb_profileimg`=:col8
                where `mb_idx`=:col1 and `mb_dregdate` is null
                ",
                array(
                    MB_IDX,
                    $req['name'],
                    $req['gender'],
                    $mb_phone,
                    $req['address1'].'|'.$req['address2'].'|'.$req['address3'],
                    $req['telephone'],
                    $mb_email_chg,
                    $profileimg_name
                )
            );

        } else {
            $sql->query(
                "
                update {$sql->table("member")}
                set `mb_pwd`=:col2, `mb_name`=:col3, `mb_gender`=:col4, `mb_phone`=:col5, `mb_address`=:col6, `mb_telephone`=:col7, `mb_email_chg`=:col8, `mb_profileimg`=:col9
                where `mb_idx`=:col1 and `mb_dregdate` is null
                ",
                array(
                    MB_IDX,
                    $MB['pwd'],
                    $req['name'],
                    $req['gender'],
                    $mb_phone,
                    $req['address1'].'|'.$req['address2'].'|'.$req['address3'],
                    $req['telephone'],
                    $mb_email_chg,
                    $profileimg_name
                )
            );
        }

        // return
        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 변경 되었습니다.'
            )
        );
        Valid::turn();
    }

    //
    // 회원 탈퇴
    //
    private function get_lv()
    {
        global $MB, $req;

        $sql = new Pdosql();
        $uploader = new Uploader();

        if ($MB['adm'] == 'Y') Valid::error('', '최고 관리자는 탈퇴할 수 없습니다.');

        // 비밀번호 입력 되었는지 검사
        if (!$req['pwd'] || !$req['pwd2']) Valid::error('pwd', '탈퇴를 위해 비밀번호를 입력하세요.');
        if ($req['pwd'] != $req['pwd2']) Valid::error('pwd2', '비밀번호와 비밀번호 확인이 일치하지 않습니다.');

        // 패스워드가 올바른지 검사
        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where `mb_idx`=:col1 and `mb_id`=:col2 and `mb_pwd`={$sql->set_password($req['pwd'])} and `mb_dregdate` is null
            ",
            array(
                MB_IDX, $MB['id']
            )
        );

        if ($sql->getcount() < 1) Valid::error('pwd', '비밀번호가 올바르지 않습니다.');

        // delete
        $sql->query(
            "
            update {$sql->table("member")}
            set `mb_dregdate`=now()
            where `mb_idx`=:col1 and `mb_dregdate` is null
            ",
            array(
                MB_IDX
            )
        );

        // 로그인 세션 삭제
        Session::drop_sess();

        // 프로필 이미지 삭제
        $uploader->path = PH_DATA_PATH.'/memberprofile';
        $uploader->drop($MB['profileimg']);

        // return
        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '탈퇴가 완료 되었습니다. 그동안 이용해 주셔서 감사합니다.',
                'location' => PH_DOMAIN,
            )
        );
        Valid::turn();
    }
}

//
// Controller for display
// https://{domain}/member/point
//
class Point extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/member/point.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {
        global $MB;

        $req = Method::request('get', 'fdate, tdate');

        $sql = new Pdosql();
        $paging = new Paging();

        Func::getlogin(SET_NOAUTH_MSG);

        // where
        $where = array();

        if ($req['fdate'] && preg_match("/^\d{4}-\d{2}-\d{2}$/", $req['fdate'])) $where[] = "regdate >= '".addslashes($req['fdate'])."'";
        if ($req['tdate'] && preg_match("/^\d{4}-\d{2}-\d{2}$/", $req['tdate'])) $where[] = "regdate < '".date('Y-m-d', strtotime(addslashes($req['tdate']) . ' +1 day'))."'";

        $where = (!empty($where)) ? ' and '.implode(' and ', $where) : '';

        // 포인트 내역 가져옴
        $sql->query(
            $paging->query(
                "
                select *
                from {$sql->table("mbpoint")}
                where `mb_idx`=:col1 {$where}
                order by `idx` desc
                ",
                array(
                    MB_IDX
                )
            )
        );
        $print_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $arr = $sql->fetchs();

                $arr['no'] = $paging->getnum();
                $arr['regdate'] = Func::datetime($arr['regdate']);
                $arr['p_in'] = Func::number($arr['p_in']);
                $arr['p_out'] = Func::number($arr['p_out']);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }
        
        $this->set('req', $req);
        $this->set('print_arr', $print_arr);
        $this->set('pagingprint', $paging->pagingprint('&fdate='.$req['fdate'].'&tdate='.$req['tdate']));
        $this->set('total_point', Func::number($MB['point']));
    }

}

//
// Controller
// ( Pointgift_pop )
//
class Pointgift_pop extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(PH_THEME_PATH.'/html/member/pointgift-pop.tpl.php');
    }

    public function make()
    {
        global $MB;

        $req = Method::request('get', 'to_mb_id, reply_parent_idx');

        $is_mbinfo_show = true;

        if (!IS_MEMBER) $is_mbinfo_show = false;

        $now_total_point = Func::number($MB['point']);

        $this->set('to_mb_id', $req['to_mb_id']);
        $this->set('reply_parent_idx', $req['reply_parent_idx']);
        $this->set('is_mbinfo_show', $is_mbinfo_show);
        $this->set('now_total_point', $now_total_point);

    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', PH_DIR.'/member/pointgift-pop-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Pointgift_pop )
//
class Pointgift_pop_submit {

    public function init()
    {
        global $MB;

        $sql = new Pdosql();
        $alarm = new Alarm_Library();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'to_mb_id, to_point, article');

        // 관리 권한 검사
        if (!IS_MEMBER) Valid::error('', '포인트를 선물할 권한이 없습니다.');

        // 회원 아이디 검증
        Valid::get(
            array(
                'input' => 'to_mb_id',
                'value' => $req['to_mb_id'],
                'check' => array(
                    'null' => false,
                    'defined' => 'id'
                )
            )
        );

        // 포인트 검증
        Valid::get(
            array(
                'input' => 'to_point',
                'value' => $req['to_point'],
                'check' => array(
                    'charset' => 'number',
                    'minint' => 10,
                    'maxint' => 99999
                )
            )
        );

        // 보유 포인트 보다 큰 금액인지 검사
        if ($req['to_point'] > $MB['point']) Valid::error('to_point', '포인트가 부족하여 선물할 수 없습니다.');

        // 자기 자신에게 발송하는지 검사
        if ($req['to_mb_id'] == $MB['id']) Valid::error('to_mb_id', '자신에게 선물할 수 없습니다.');

        // 존재하는 회원인지 검사
        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where `mb_id`=:col1 and `mb_dregdate` is null
            ",
            array(
                $req['to_mb_id']
            )
        );
        if ($sql->getcount() < 1) Valid::error('to_mb_id', '존재하지 않는 회원 아이디 입니다.');

        $to_mb_arr = $sql->fetchs();

        // 내용 검증
        Valid::get(
            array(
                'input' => 'article',
                'value' => $req['article'],
                'check' => array(
                    'null' => false,
                    'minlen' => 5,
                    'maxlen' => 30
                )
            )
        );

        $to_msg = preg_replace('/\r\n|\r|\n/', '', $req['article']);

        // 회원에게 포인트 발송
        Func::set_mbpoint(
            array(
                'mode' => 'in',
                'mb_idx' => $to_mb_arr['mb_idx'],
                'point' => $req['to_point'],
                'msg' => $MB['name'].'('.$MB['id'].')님이 포인트를 선물했습니다. ('.$to_msg.')'
            )
        );

        // 자신의 포인트 차감
        Func::set_mbpoint(
            array(
                'mode' => 'out',
                'mb_idx' => $MB['idx'],
                'point' => $req['to_point'],
                'msg' => $to_mb_arr['mb_name'].'('.$to_mb_arr['mb_id'].')님에게 포인트를 선물했습니다. ('.$to_msg.')'
            )
        );

        // 받는 회원에게 알림 발송
        $alarm->get_add_alarm(
            array(
                'msg_from' => $MB['name'].'회원 ('.$MB['id'].')',
                'from_mb_idx' => $MB['idx'],
                'to_mb_idx' => $to_mb_arr['mb_idx'],
                'memo' => '<strong>'.$MB['name'].'</strong>님이 <strong>'.Func::number($req['to_point']).'</strong>포인트를 선물 했습니다. ('.$to_msg.')',
                'link' => PH_DIR.'/member/point'
            )
        );

        // return
        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '포인트를 성공적으로 선물 하였습니다.'
            )
        );
        Valid::turn();

    }

}

//
// Controller for display
// https://{domain}/member/warning
//
class Warning extends \Controller\Make_Controller {

    public function init()
    {
        $this->common()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/member/warning.tpl.php');
        $this->common()->foot();
    }

    public function make()
    {
        global $MB, $ip_qry;

        $sql = new Pdosql();

        Blocked::get_qry();

        $sql->query(
            "
            select *, count(*) as total
            from {$sql->table("blockmb")}
            where (`ip`=:col1 or `ip`=:col2 or `ip`=:col3 or `ip`=:col4) or (`mb_idx`=:col5 and `mb_id`=:col6)
            limit 1
            ",
            array(
                $ip_qry[0],
                $ip_qry[1],
                $ip_qry[2],
                $ip_qry[3],
                $MB['idx'],
                $MB['id']
            )
        );

        if ($sql->fetch('total') < 1) Func::err_location('차단 내역이 없습니다.', PH_DOMAIN);

        $this->set('msg', $sql->fetch('memo'));
    }

}
