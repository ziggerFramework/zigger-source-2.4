<?php
// 팝업 출력
$this->popup_fetch();
?>

<div class="vis">
    <div class="in">
        <i class="fa fa-info-circle"></i>
        <h3>zigger simple theme로<br />웹사이트 구축이 완료 되었습니다.</h3>
        <p>
            zigger simple theme는 zigger의 Identity를 담은 기본형 Theme입니다.<br />
            simple theme를 활용해 빠르게 웹사이트 레이아웃을 디자인하세요.
        </p>
    </div>
</div>

<div class="lat-wrap">

    <div class="lat">
        <a href="<?php echo PH_DIR; ?>/sub/board/news" class="more"><i class="fa fa-plus"></i><p>더보기</p></a>
        <?php
        // 최근게시물 출력
        $this->latest_fetch();
        ?>
    </div>

    <div class="lat">
        <a href="<?php echo PH_DIR; ?>/sub/board/free" class="more"><i class="fa fa-plus"></i><p>더보기</p></a>
        <?php
        // 최근게시물 출력
        $this->latest_fetch2();
        ?>
    </div>

</div>

<div class="mid-bn">
    <?php
    // 배너 출력
    $this->banner_fetch();
    ?>
</div>
