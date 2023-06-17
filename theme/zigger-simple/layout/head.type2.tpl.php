<header id="header" class="type2">
    <div class="inner">

        <div id="skip-to-article"><a href="#content">본문 바로가기</a></div>

        <a href="<?php echo $this->layout->site_href(); ?>" class="logo">
            <h1><img src="<?php echo $this->layout->logo_src(); ?>" alt="<?php echo $this->layout->logo_title(); ?>"></h1>
        </a>

    </div>

</header>

<?php if (defined('MAINPAGE')) { ?>
<!-- Main page -->
<div id="main">
    <div id="content">

<?php } else { ?>
<!-- Sub page -->
<div id="sub">
    <div id="content">
<?php } ?>
