<?php
namespace Module\Contactform;

use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Make\Library\Mail;

//
// Module Controller
// ( Form )
//
class Form extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_CONTACTFORM_THEME_PATH.'/form.tpl.php');
    }

    public function make()
    {
        $this->set('captcha', Func::get_captcha('', 1));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'contactForm');
        $form->set('type', 'html');
        $form->set('action', MOD_CONTACTFORM_DIR.'/controller/form/contactus-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Contactus )
//
class Contactus_submit {

    public function init()
    {
        global $MODULE_CONTACTFORM_CONF, $CONF;

        $sql = new Pdosql();
        $mail = new Mail();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'name, email, phone, article, captcha, contact_1, contact_2, contact_3, contact_4, contact_5, contact_6, contact_7, contact_8, contact_9, contact_10');

        Valid::get(
            array(
                'input' => 'name',
                'value' => $req['name']
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
                    'defined' => 'phone'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'article',
                'value' => $req['article']
            )
        );

        if (!IS_MEMBER) {
            if (!Func::chk_captcha($req['captcha'])) {
                Valid::set(
                    array(
                        'return' => 'error',
                        'input' => 'captcha',
                        'err_code' => 'NOTMATCH_CAPTCHA'
                    )
                );
                Valid::turn();
            }
        }

        // insert
        $sql->query(
            "
            insert into {$sql->table("mod:contactform")}
            (mb_idx, article, name, email, phone, regdate, contact_1, contact_2, contact_3, contact_4, contact_5, contact_6, contact_7, contact_8, contact_9, contact_10)
            values
            (:col1, :col2, :col3, :col4, :col5, now(), :col6, :col7, :col8, :col9, :col10, :col11, :col12, :col13, :col14, :col15)
            ",
            array(
                MB_IDX, $req['article'], $req['name'], $req['email'], $req['phone'], $req['contact_1'], $req['contact_2'], $req['contact_3'],
                $req['contact_4'], $req['contact_5'], $req['contact_6'], $req['contact_7'],$req['contact_8'], $req['contact_9'], $req['contact_10']
            )
        );

        // mail
        $memo = '
            새로운 문의가 등록되었습니다.<br /><br />
            <a href="'.PH_DOMAIN.PH_DIR.'/manage/mod/'.MOD_CONTACTFORM.'/result/result">'.PH_DOMAIN.PH_DIR.'/manage/mod/'.MOD_CONTACTFORM.'/result/result</a> 를 클릭하여<br />
            관리 페이지로 접속 후 확인하세요.
        ';
        $mail->set(
            array(
                'to' => array(
                    [
                        'email' => $CONF['email']
                    ]
                ),
                'subject' => '새로운 문의가 등록되었습니다.',
                'memo' => $memo
            )
        );
        $mail->send();

        // 관리자 피드에 등록
        Func::add_mng_feed(
            array(
                'from' => $MODULE_CONTACTFORM_CONF['title'],
                'msg' => '<strong>'.$req['name'].'</strong>님이 새로운 문의를 등록했습니다.',
                'link' => '/manage/mod/'.MOD_CONTACTFORM.'/result/result'
            )
        );

        // return
        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '문의가 정상적으로 접수 되었습니다.',
            )
        );
        Valid::turn();
    }

}
