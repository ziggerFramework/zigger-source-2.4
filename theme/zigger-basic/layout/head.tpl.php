<header id="header">
    <div class="inner">

        <div id="skip-to-article"><a href="#content">본문 바로가기</a></div>

        <a href="<?php echo $this->layout->site_href(); ?>" class="logo">
            <h1><img src="<?php echo $this->layout->logo_src(); ?>" alt="<?php echo $this->layout->logo_title(); ?>"></h1>
        </a>

        <div id="sch">
            <form action="<?php echo PH_DIR; ?>/search">
                <fieldset>
                    <legend>통합검색</legend>
                    <input type="text" name="keyword" id="keyword" class="inp" />
                    <label for="keyword" class="sound_only_ele">검색어 <strong>필수 입력</strong></label>
                    <button type="submit" class="sbm">검색</button>
                </fieldset>
            </form>
        </div>

        <ul id="tnb">
            <?php if (!IS_MEMBER) { ?>
            <li><a href="<?php echo $this->layout->signin_href(); ?>">회원로그인</a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/sign/signup">회원가입</a></li>

            <?php } else { ?>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/sign/signout">로그아웃</a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/member">Mypage</a></li>
            <?php if ($MB['level'] == 1) { ?>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/manage/">Manage</a></li>
            <?php } ?>
            <?php } ?>
        </ul>

    </div>

    <nav>
        <ul id="gnb">
            <?php foreach ($SITEMAP as $gnb) { ?>
            <li>
                <a href="<?php echo $gnb['href']; ?>" data-category-key="<?php echo $gnb['idx']; ?>" target="<?php echo $gnb['target']; ?>"><?php echo $gnb['title']; ?></a>
                <?php if (count($gnb['2d']) > 0) { ?>
                <div class="sound_only_ele">하위 메뉴</div>
                <ul>
                    <?php foreach ($gnb['2d'] as $gnb2) { ?>
                    <li>
                        <a href="<?php echo $gnb2['href']; ?>" data-category-key="<?php echo $gnb2['idx']; ?>" target="<?php echo $gnb2['target']; ?>"><?php echo $gnb2['title']; ?></a>
                        <?php if (count($gnb2['3d']) > 0) { ?>
                        <ul>
                            <?php foreach ($gnb2['3d'] as $gnb3) { ?>
                            <li><a href="<?php echo $gnb3['href']; ?>" data-category-key="<?php echo $gnb3['idx']; ?>" target="<?php echo $gnb3['target']; ?>"><?php echo $gnb3['title']; ?></a></li>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>

        <ul id="util_menu">
            <li class="<?php echo ($this->layout->message_new_count() != 0) ? 'new' : ''; ?>">
                <a href="<?php echo $this->layout->site_dir(); ?>/message">
                    <div class="sound_only_ele">새로운 메시지 보기</div>
                    <i class="fas fa-comment-alt"></i><strong><?php echo $this->layout->message_new_count(); ?></strong>
                </a>
            </li>
            <li class="<?php echo ($this->layout->alarm_new_count() != 0) ? 'new' : ''; ?>">
                <a href="<?php echo $this->layout->site_dir(); ?>/alarm">
                    <div class="sound_only_ele">새로운 알림 보기</div>
                    <i class="fas fa-bell"></i><strong><?php echo $this->layout->alarm_new_count(); ?></strong>
                </a>
            </li>
        </ul>
    </nav>

    <div href="#" id="slide-btn">
        <button><span></span></button>
        전체 메뉴 열기
    </div>

</header>

<!-- for mobile -->
<div id="slide-menu">
    <div class="inner">
        <ul id="mo-tnb">
            <?php if (!IS_MEMBER) { ?>
            <li><a href="<?php echo $this->layout->signin_href(); ?>">회원로그인</a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/sign/signup">회원가입</a></li>
            <?php } else { ?>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/message">Message <em><?php echo $this->layout->message_new_count(); ?></em></a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/alarm">Alarm <em><?php echo $this->layout->alarm_new_count(); ?></em></a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/sign/signout">로그아웃</a></li>
            <li><a href="<?php echo $this->layout->site_dir(); ?>/member">Mypage</a></li>
            <?php } ?>
        </ul>

        <div id="mo-sch">
            <form action="<?php echo PH_DIR; ?>/search">
                <fieldset>
                    <legend>통합검색</legend>
                    <input type="text" name="keyword" class="inp" />
                    <button type="submit" class="sbm">검색</button>
                </fieldset>
            </form>
        </div>

        <ul id="mo-gnb">
            <?php foreach($SITEMAP as $gnb) { ?>
            <li>
                <a href="<?php echo $gnb['href']; ?>" data-category-key="<?php echo $gnb['idx']; ?>" target="<?php echo $gnb['target']; ?>"><?php echo $gnb['title']; ?></a>
                <?php if (count($gnb['2d']) > 0) { ?>
                <div class="sound_only_ele">하위 메뉴</div>
                <ul>
                    <?php foreach ($gnb['2d'] as $gnb2) { ?>
                    <li>
                        <a href="<?php echo $gnb2['href']; ?>" data-category-key="<?php echo $gnb2['idx']; ?>" target="<?php echo $gnb2['target']; ?>"><?php echo $gnb2['title']; ?></a>
                        <?php if (count($gnb2['3d']) > 0) { ?>
                        <ul>
                            <?php foreach ($gnb2['3d'] as $gnb3) { ?>
                            <li><a href="<?php echo $gnb3['href']; ?>" data-category-key="<?php echo $gnb3['idx']; ?>" target="<?php echo $gnb3['target']; ?>"><?php echo $gnb3['title']; ?></a></li>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>

        <button type="button" id="slide-menu-close" class="sound_only_ele">전체 메뉴 닫기</button>
    </div>
</div>
<div id="slide-bg"></div>

<?php if (defined('MAINPAGE')) { ?>
<!-- Main page -->
<div id="main">
    <div id="content">

<?php } else { ?>
<!-- Sub page -->
<div id="sub">

    <?php if ($NAVIGATOR) { ?>
    <div class="sub-vis">
        <div class="in">
            <h3><?php echo $NAVIGATOR[0]['title']; ?></h3>
            <ul id="navi">
                <li>
                    <a href="<?php echo $this->layout->site_dir(); ?>/"><?php echo $this->layout->logo_title(); ?></a>
                </li>
                <?php foreach ($NAVIGATOR as $navigt) { ?>
                <li>
                    <i class="fa fa-angle-right"></i>
                    <a href="<?php echo $this->layout->site_dir(); ?>/<?php echo $navigt['href']; ?>"><?php echo $navigt['title']; ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php } ?>

    <?php if (isset($LNB_SITEMAP['2d']) && count($LNB_SITEMAP['2d']) > 0) { ?>
    <div class="lnb-wrap">
        <ul id="lnb">
            <?php foreach ($LNB_SITEMAP['2d'] as $gnb) { ?>
            <li>
                <a href="<?php echo $gnb['href']; ?>" data-category-key="<?php echo $gnb['idx']; ?>"><?php echo $gnb['title']; ?></a>
                <?php if (count($gnb['3d']) > 0) { ?>
                <ul>
                    <?php foreach ($gnb['3d'] as $gnb2) { ?>
                    <li><a href="<?php echo $gnb2['href']; ?>" data-category-key="<?php echo $gnb2['idx']; ?>"><?php echo $gnb2['title']; ?></a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>

    <div id="content">

        <?php if ($NAVIGATOR) { ?>
        <div id="sub-tit">
            <h2><?php echo $NAVIGATOR[count($NAVIGATOR)-1]['title']; ?></h2>
        </div>
        <?php } ?>

<?php } ?>
