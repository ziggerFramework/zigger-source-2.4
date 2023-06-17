<div id="sub-tit">
    <h2>테마 설정</h2>
    <em><i class="fa fa-exclamation-circle"></i>사이트 테마 설정 (테마 설치 경로 : /theme/)</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="theme"><em>전체테마</em><p><?php echo $theme_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <ul class="theme-list">
            <?php foreach ($print_arr as $list) { ?>
            <li>
                <label class="rdo __label"><input type="radio" name="theme_slt" value="<?php echo $list['theme']; ?>" <?php if ($list['theme'] == $CONF['theme']) echo "checked"; ?> /></label>
                <span class="tit"><?php echo $list['name']; ?></span>
                <div class="tmb"><img src="<?php echo $list[0]['thumbnail']; ?>" /></div>
                <ul class="info">
                    <li><strong>제작자</strong><p><?php echo $list['developer']; ?></p></li>
                    <li><strong>버전</strong><p><?php echo $list['version']; ?></p></li>
                    <li><strong>제작일</strong><p><?php echo $list['develDate']; ?></p></li>
                    <li><strong>업데이트일</strong><p><?php echo $list['updateDate']; ?></p></li>
                    <li><strong>제작자 web</strong><p><?php echo $list['website']; ?></p></li>
                </ul>
            </li>
            <?php } ?>
        </ul>

    </form>

</article>
