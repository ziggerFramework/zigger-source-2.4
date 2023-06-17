<input type="hidden" name="type" value="add" />
<input type="hidden" name="new_caidx" value="" />

<div class="sortable">

    <?php foreach ($print_arr as $list) { ?>
        <div class="st-1d">
            <h4><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="<?php echo $list['idx']; ?>" /><input type="hidden" name="org_caidx[]" value="<?php echo $list['caidx']; ?>" /><input type="hidden" name="caidx[]" value="<?php echo $list['caidx']; ?>" data-depth="1" /><?php echo $list['title']; ?></a><i class="fa fa-trash-alt st-del del-1d ajbtn"></i></h4>
        </div>
    <?php } ?>

</div>
<a href="#" class="btn1 no-mar add-1d ajbtn"><i class="fa fa-plus"></i> 통합검색 콘텐츠 추가</a>
