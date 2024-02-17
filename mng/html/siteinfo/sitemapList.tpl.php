<input type="hidden" name="type" value="add" />
<input type="hidden" name="new_caidx" value="" />

<div class="sortable">

    <?php foreach ($print_arr as $list) { ?>
        <div class="st-1d">
            <h4><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="<?php echo $list['idx']; ?>" /><input type="hidden" name="org_caidx[]" value="<?php echo $list['caidx']; ?>" /><input type="hidden" name="caidx[]" value="<?php echo $list['caidx']; ?>" data-depth="1" /><?php echo $list['title']; ?></a><i class="fa fa-trash-alt st-del del-1d ajbtn"></i></h4>
            <div class="in">
                <ul class="st-2d">

                    <?php foreach ($list['2d'] as $list2) { ?>
                        <li>
                            <p><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="<?php echo $list2['idx']; ?>" /><input type="hidden" name="org_caidx[]" value="<?php echo $list2['caidx']; ?>" /><input type="hidden" name="caidx[]" value="<?php echo $list2['caidx']; ?>" data-depth="2" /><?php echo $list2['title']; ?></a><i class="fa fa-plus add-3d ajbtn"></i><i class="fa fa-trash-alt st-del del-2d ajbtn"></i></p>
                            <ul class="st-3d">

                                <?php foreach ($list2['3d'] as $list3) { ?>
                                    <li><p><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="<?php echo $list3['idx']; ?>" /><input type="hidden" name="org_caidx[]" value="<?php echo $list3['caidx']; ?>" /><input type="hidden" name="caidx[]" value="<?php echo $list3['caidx']; ?>" data-depth="3" /><?php echo $list3['title']; ?></a><i class="fa fa-trash-alt st-del del-3d ajbtn"></i></p></i></li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>

                </ul>
                <?php if (count($list['2d']) < 1) { ?>
                <span class="st-no-cat">아직 생성된 2차 카테고리가 없습니다.</span>
                <?php } ?>

            </div>
            <a href="#" class="st-add add-2d ajbtn"><i class="fa fa-plus"></i> 2차 카테고리 추가</a>
        </div>
    <?php } ?>

</div>
<a href="#" class="btn1 no-mar add-1d ajbtn"><i class="fa fa-plus"></i> 1차 카테고리 추가</a>
