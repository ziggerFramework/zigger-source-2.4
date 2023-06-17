<h4>
    <a href="<?=$get_board_link?>"><?=$board_title?></a>
</h4>

<?php
if ($print_arr) {
    foreach ($print_arr as $list) {
?>
<dl>
    <dt>
        <a href="<?=$list[0]['get_link']?>" class="tmb">
            <img src="<?=$list[0]['thumbnail']?>" width="<?=$list['img-width']?>" height="<?=$list['img-height']?>" />
        </a>
    </dt>
    <dd>
        <a href="<?=$list[0]['get_link']?>" class="sbj">
            <?=$list[0]['print_subject']?>
            <em><?=$list[0]['comment_cnt']?></em>
        </a>
        <a href="<?=$list[0]['get_link']?>" class="article">
            <?=$list[0]['print_article']?>
        </a>
        <span class="writer"><?=$list['writer']?></span>
        <span class="date"><?=$list['date']?></span>
    </dd>
</dl>
<?php }} ?>

<?php if (!$print_arr) { ?>
    <p class="no-data">게시글 <?=SET_NODATA_MSG?></p>
<?php } ?>
