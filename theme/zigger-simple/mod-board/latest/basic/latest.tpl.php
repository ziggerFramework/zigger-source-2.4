<h4>
    <a href="<?=$get_board_link?>"><?=$board_title?></a>
</h4>

<?php if ($print_arr) { ?>
<ul>
    <?php foreach ($print_arr as $list) { ?>
    <li>
        <a href="<?=$list[0]['get_link']?>" class="sbj">
            <?=$list[0]['print_subject']?>
            <em><?=$list[0]['comment_cnt']?></em>
        </a>
        <span class="date"><?=$list['date']?></span>
        <span class="writer"><?=$list['writer']?></span>
    </li>
    <?php } ?>
</ul>
<?php } ?>

<?php if (!$print_arr) { ?>
<p class="no-data">게시글 <?=SET_NODATA_MSG?></p>
<?php } ?>
