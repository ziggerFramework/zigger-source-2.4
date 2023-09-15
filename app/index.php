<?php

define('MAINPAGE', TRUE);

//
// Controller for display
// https://{domain}
//
class Index extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->head();
        $this->layout()->view(PH_THEME_PATH.'/html/index.tpl.php');
        $this->layout()->foot();
    }

    public function make()
    {

    }

    // 팝업 Fetch
    public function popup_fetch()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', PH_PATH.'/lib/popup.fetch.php');
        $fetch->run();
    }

    // 배너 Fetch
    public function banner_fetch()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', PH_PATH.'/lib/banner.fetch.php');
        $fetch->set('key', 'test_banner'); // 배너 key
        $fetch->run();
    }

    // 최근게시물 Fetch
    public function latest_fetch()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', MOD_BOARD_PATH.'/lib/latest.fetch.php');
        $fetch->set('id', 'news'); // 게시판 id
        $fetch->set('theme', 'basic'); // 출력 테마
        $fetch->set('orderby', 'recent'); // 출력 기준 ('recent' or 'view' or 'like' / 기본값: 'recent')
        $fetch->set('limit', 5); // 출력 개수
        $fetch->set('subject', 30); // 제목 글자 수 (기본값: 30)
        $fetch->set('uri', PH_DIR.'/sub/board/news'); // 클릭시 이동할 page uri
        $fetch->run();
    }

    // 최근게시물 Fetch
    public function latest_fetch2()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', MOD_BOARD_PATH.'/lib/latest.fetch.php');
        $fetch->set('id', 'freeboard'); // 게시판 id
        $fetch->set('theme', 'webzine'); // 출력 테마
        $fetch->set('orderby', 'recent'); // 출력 기준 ('recent' or 'view' or 'like' / 기본값: 'recent')
        $fetch->set('limit', 2); // 출력 개수
        $fetch->set('subject', 30); // 제목 글자 수 (기본값: 30)
        $fetch->set('article', 50); // 내용 글자 수 (기본값: 50)
        $fetch->set('img-width', 150); // 썸네일 폭 (기본값: 150)
        $fetch->set('img-height', 150); // 썸네일 높이 (기본값: 150)
        $fetch->set('uri', PH_DIR.'/sub/board/free'); // 클릭시 이동할 page uri
        $fetch->run();
    }

}
