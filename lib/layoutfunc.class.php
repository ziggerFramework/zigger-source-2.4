<?php
namespace Make\View;

use Corelib\Func;
use Module\Message\Library as Message_Library;
use Module\Alarm\Library as Alarm_Library;

class Layout {

    // 사이트명 반환
    public function logo_title()
    {
        global $CONF;
        return $CONF['title'];
    }

    // 사이트 주소 반환
    public function site_href()
    {
        return PH_DOMAIN;
    }

    // 사이트 경로 반환
    public function site_dir()
    {
        return PH_DOMAIN;
    }

    // 로고 이미지 경로 반환
    public function logo_src()
    {
        global $CONF;

        return ($CONF['logo']) ? $CONF['logo'] : PH_THEME_DIR.'/layout/images/logo.png';
    }

    // 회원가입 url 반환
    public function signin_href()
    {
        $link = PH_DIR.'/sign/signin?redirect='.urlencode(Func::thisuriqry());

        if (Func::thisctrlr() == 'sign' || Func::thisctrlr() == 'member') $link = PH_DIR.'/sign/signin?redirect=/';

        return $link;
    }

    // message module 새로운 알림 개수 반환
    public function message_new_count()
    {
        $Message_Library = new Message_Library();

        return Func::number($Message_Library->get_new_count());
    }

    // alarm module 새로운 알림 개수 반환
    public function alarm_new_count()
    {
        $Alarm_Library = new Alarm_Library();

        return Func::number($Alarm_Library->get_new_count());
    }

}
