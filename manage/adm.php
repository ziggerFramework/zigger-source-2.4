<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Uploader;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/adm/info
//
class Info extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(PH_MANAGE_PATH.'/html/adminfo.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function set_checked($arr, $val)
        {
            $setarr = array(
                'Y' => '',
                'N' => '',
                'M' => '',
                'F' => ''
            );

            foreach ($setarr as $key => $value) {
                if ($key == $arr[$val]) $setarr[$key] = 'checked';
            }

            return $setarr;
        }
    }

    public function make()
    {
        global $MB;

        $manage = new ManageFunc();

        if ($MB['adm'] != 'Y') $func->err_back(ERR_MSG_1);

        $profileimg = '';
        if ($MB['profileimg']) {
            $fileinfo = Func::get_fileinfo($MB['profileimg']);
            $profileimg = $fileinfo['replink'];
        }

        $MB[0]['address'] = explode('|', $MB['address']);

        if (!$MB[0]['address'][0]) $MB[0]['address'] = array('', '', '');

        $this->set('manage', $manage);
        $this->set('profileimg', $profileimg);
        $this->set('gender', set_checked($MB, 'gender'));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'adminfoForm');
        $form->set('type', 'multipart');
        $form->set('action', PH_MANAGE_DIR.'/adm/info-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Info )
//
class Info_submit{

    public function init()
    {
        global $MB;

        $sql = new Pdosql();
        $uploader = new Uploader();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id, name, pwd, pwd2, email, gender, phone, telephone, address1, address2, address3');
        $file = Method::request('file', 'profileimg');
        $manage->req_hidden_inp('post');

        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'name',
                'value' => $req['name'],
                'check' => array(
                    'defined' => 'nickname'
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
                'input' => 'phone',
                'value' => $req['phone'],
                'check' => array(
                    'null' => true,
                    'defined' => 'phone'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'telephone',
                'value' => $req['telephone'],
                'check' => array(
                    'null' => true,
                    'defined' => 'phone'
                )
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where mb_id=:col1 and mb_dregdate is null and mb_adm!='Y'
            ",
            array(
                $req['id']
            )
        );

        if ($sql->getcount() > 0) Valid::error('id', '이미 존재하는 아이디입니다.');

        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where mb_id=:col1 and mb_dregdate is null
            ",
            array(
                $req['email']
            )
        );

        if ($sql->getcount() > 0) Valid::error('email', '다른 회원이 사용중인 email 입니다.');
        if ($req['pwd'] != $req['pwd2']) Valid::error('pwd2', '비밀번호와 비밀번호 확인이 일치하지 않습니다.');

        $uploader->path= PH_DATA_PATH.'/memberprofile';
        $uploader->chkpath();

        $profileimg_name = '';

        if (isset($file['profileimg'])) {
            $uploader->file = $file['profileimg'];
            $uploader->intdict = SET_IMGTYPE;

            if ($uploader->chkfile('match') !== true) Valid::error('profileimg', '허용되지 않는 프로필 이미지 유형입니다.');
            $profileimg_name = $uploader->replace_filename($file['profileimg']['name']);
            if (!$uploader->upload($profileimg_name)) Valid::error('profileimg', '프로필 이미지 업로드 실패');
        }

        if ((isset($file['profileimg']) && $MB['profileimg'] != '')) $uploader->drop($MB['profileimg']);
        if ($MB['profileimg'] != '' && !isset($file['profileimg'])) $profileimg_name = $MB['profileimg'];

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

            $sql->query(
                "
                update {$sql->table("member")}
                set mb_id=:col2, mb_name=:col3, mb_pwd={$sql->set_password($req['pwd'])}, mb_email=:col4, mb_profileimg=:col5, mb_gender=:col6, mb_phone=:col7, mb_telephone=:col8, mb_address=:col9
                where mb_adm='Y' and mb_idx=:col1
                ",
                array(
                    $MB['idx'], $req['id'], $req['name'], $req['email'], $profileimg_name, $req['gender'], $req['phone'], $req['telephone'], $req['address1'].'|'.$req['address2'].'|'.$req['address3']
                )
            );

        } else {

            $sql->query(
                "
                update {$sql->table("member")}
                set mb_id=:col2, mb_name=:col3, mb_pwd=:col4, mb_email=:col5, mb_profileimg=:col6, mb_gender=:col7, mb_phone=:col8, mb_telephone=:col9, mb_address=:col10
                where mb_adm='Y' and mb_idx=:col1
                ",
                array(
                    $MB['idx'], $req['id'], $req['name'], $MB['pwd'], $req['email'], $profileimg_name, $req['gender'], $req['phone'], $req['telephone'], $req['address1'].'|'.$req['address2'].'|'.$req['address3']
                )
            );

        }

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 변경 되었습니다.'
            )
        );
        Valid::turn();
    }

}
