

<div id="board-list">
    <ul>
    <?php foreach ($print_arr as $list) { ?>
        <li <?php if ($list[0]['thumbnail'] != SET_BLANK_IMG) { ?>class="with-tmb"<?php } ?>>
            <a href="<?=$list[0]['get_link']?>">
                <?php if ($list[0]['thumbnail'] != SET_BLANK_IMG) { ?>
                <div class="tmb" style="background-image: url('<?php echo $list[0]['thumbnail']; ?>');"></div>
                <?php } ?>

                <?=$list[0]['print_subject']?>
                <span class="cmt"><?php echo $list[0]['comment_cnt']; ?></span>
                <span class="txt">
                    <?php
                    $article = $list[0]['print_article'];
                    if (strstr($article, '[/@]')) $article = mb_substr($article, mb_strpos($article, '[/@]') + 4);
                    echo str_replace(array('[!]', '[/!]'), array('', ''), $article);
                    ?>
                </span>
            </a>
        </li>
    <?php } ?>
    </ul>

    <!-- no data -->
    <?php if (!$print_arr) { ?>
    <p id="board-nodata"><?php echo SET_NODATA_MSG; ?></p>
    <?php } ?>
</div>
