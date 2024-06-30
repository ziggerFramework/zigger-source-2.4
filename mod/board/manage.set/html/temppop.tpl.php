<div class="tit">
	<h2>저장된 나의 임시글</h2>
	<a href="#" class="close"><i class="fa fa-times"></i></a>
</div>

<div class="cont">

	<!-- 임시글 표시 (최근 30개만 노출) -->
    <p class="sment">임시저장 글은 최근 30개까지 보관됩니다.</p>
    
    <form <?php $this->form(); ?>>
        <input type="hidden" name="temp_hash" value="" />

        <?php if ($print_arr) { ?>
        <table class="tbl">
            <colgroup>
                <col style="width: auto;" />
                <col style="width: 140px;" />
                <col style="width: 40px;" />
            </colgroup>
            <tbody>
                <?php foreach ($print_arr as $list) { ?>
                <tr>
                    <td>
                        <a href="#" class="sbj" data-temphash="<?php echo $list['hash']; ?>"><?php echo $list['subject']; ?></a>
                    </td>
                    <td>
                        <span class="date"><?php echo $list['regdate']; ?></span> 
                    </td>
                    <td>
                    <a href="#" class="remove-btn" data-temphash="<?php echo $list['hash']; ?>"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </form>

    <!-- no data -->
    <p id="list-nodata" style="<?php echo ($print_arr) ? 'display: none;' : ''; ?>"><?php echo SET_NODATA_MSG; ?></p>
</div>
