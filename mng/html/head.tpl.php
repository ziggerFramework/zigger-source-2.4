<header id="header">
    <a href="<?php echo PH_DIR; ?>/manage/" class="logo">
        <h1><img src="<?php echo PH_MANAGE_DIR; ?>/images/logo.svg" alt="zigger Manager"></h1>
    </a>
    <ul id="tnb">
        <li><a href="<?php echo $manage->gosite(); ?>">웹사이트</a></li>
        <?php if ($MB['adm']=="Y") { ?>
            <li><a href="<?php echo $manage->adminfo_link(); ?>">관리자 정보 변경</a></li>
        <?php } ?>
        <li><a href="<?php echo $manage->signout_link(); ?>">로그아웃</a></li>
    </ul>
</header>

<div id="wrap">
    <div id="side">

        <ul class="tab">
            <li class="on"><a href="#" data-tab="def">기본메뉴</a></li>
            <li><a href="#" data-tab="mod">모듈<em><?php echo $manage->module_total(); ?></em></a></li>
        </ul>

        <div id="gnb">

            <!-- 기본 메뉴 -->
            <ul class="menu">
                <li>
                    <a href="#">기본 관리도구</a>
                    <ul>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/info">기본정보 관리</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/plugins">플러그인 및 기능 설정</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/seo">검색엔진 최적화</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/sitemap">사이트맵 관리</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">테마&amp;모듈</a>
                    <ul>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/theme/theme">테마 설정</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/theme/module">설치된 모듈</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">회원관리</a>
                    <ul>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/member/result"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/member/modify"
                            >가입 회원 관리</a>
                        </li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/member/regist">신규 회원 추가</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/member/unsigned">탈퇴 회원</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/member/record">회원 접속 기록</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/member/session">현재 접속 세션</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/member/point">포인트 관리</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">팝업</a>
                    <ul>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/popup/result"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/popup/modify"
                            >생성된 팝업</a>
                        </li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/popup/regist">신규 팝업 생성</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">배너</a>
                    <ul>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/banner/result"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/banner/modify"
                            >생성된 배너</a>
                        </li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/banner/regist">배너 생성</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">메일발송</a>
                    <ul>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/mailler/template"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/mailler/regist|<?php echo PH_MANAGE_DIR; ?>/mailler/modify"
                            >메일 템플릿 관리</a>
                        </li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/mailler/send">회원 메일 발송</a></li>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/mailler/history"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/mailler/historyview"
                            >메일 발송 내역</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">SMS발송</a>
                    <ul>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/sms/tomember">회원 SMS 발송</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/sms/send">비회원 SMS 발송</a></li>
                        <li>
                            <a
                            href="<?php echo PH_MANAGE_DIR; ?>/sms/history"
                            data-idx-href="<?php echo PH_MANAGE_DIR; ?>/sms/historyview"
                            >SMS 발송 내역</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">접속 차단</a>
                    <ul>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/block/ip">아이피 접속 차단</a></li>
                        <li><a href="<?php echo PH_MANAGE_DIR; ?>/block/member">회원 접속 차단</a></li>
                    </ul>
                </li>
            </ul>

            <!-- 모듈 -->

            <ul class="menu">
                <?php
                foreach ($gnb_arr as $key => $value) {
                    if (count($gnb_arr[$key]) < 3) continue;
                ?>
                <li>
                    <a href="#"><?php echo $gnb_arr[$key]['name']; ?></a>
                    <ul>
                        <?php
                        foreach ($gnb_arr[$key] as $key2 => $value2) {
                            if (is_int($key2)) {
                                ?>
                                <li>
                                    <a
                                    href="<?php echo PH_MANAGE_DIR."/mod/".$gnb_arr[$key]['mod']."/".$gnb_arr[$key][$key2]['href']; ?>"
                                    <?php
                                    if ($gnb_arr[$key][$key2]['data-idx-href']) {
                                        $arr = $gnb_arr[$key][$key2]['data-idx-href'];
                                    ?>
                                    data-idx-href="<?php
                                    $pipe = '';
                                    for ($i=0; $i < count($arr); $i++) {
                                        echo $pipe.PH_MANAGE_DIR."/mod/".$gnb_arr[$key]['mod']."/".$arr[$i];
                                        $pipe = '|';
                                    }
                                    ?>"
                                    <?php } ?>
                                    >
                                    <?php echo $gnb_arr[$key][$key2]['title']; ?>
                                    </a>
                                </li>
                        <?php }} ?>
                        </ul>
                    </li>
                <?php } ?>
                </ul>

            </div>
        </div>

        <div id="content">
