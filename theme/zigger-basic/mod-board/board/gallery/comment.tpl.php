<form <?php echo $this->form(); ?>>
	<input type="hidden" name="mode" value="write" />
	<input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
	<input type="hidden" name="read" value="<?php echo $read; ?>" />
	<input type="hidden" name="cidx" value="" />
	<input type="hidden" name="thisuri" value="<?php echo $thisuri; ?>" />

	<!-- total -->
	<span class="total"><strong><?php echo $total_cnt; ?></strong> 개의 댓글이 등록되어 있습니다.</span>

	<!-- 댓글 입력 -->
	<?php if ($is_writeform_show) { ?>
	<div id="comm-form">

		<?php if ($is_guest_form_show) { ?>
		<div class="gue-writer">
			<input type="text" name="writer" title="작성자" placeholder="작성자" maxlength="8" class="inp" value="<?php echo $MB['name']; ?>" />
		</div>
		<?php } ?>

		<fieldset>
			<textarea name="comment" title="댓글내용" placeholder="댓글내용"></textarea>
			<input type="button" value="작성" class="btn1 sbm" />
		</fieldset>

	</div>
	<?php } ?>

	<!-- 댓글 리스트 -->
	<?php if ($total_cnt > 0) { ?>
	<ul id="comm-list">
		<?php
		foreach ($print_arr as $list) {
		?>
		<li class="comm-list-li <?php echo ($list['rn'] > 0) ? 'reply' : ''; ?>">
			<div class="comm-btn">
				<?php echo $list[0]['reply_btn']; ?><?php echo $list[0]['modify_btn']; ?><?php echo $list[0]['delete_btn']; ?>
			</div>
			<div class="info">
				<span class="writer">
					<?php if ($list['rn'] > 0) { ?><img src="<?php echo MOD_BOARD_THEME_DIR; ?>/images/reply-ico.png" /><?php } ?>

                    <?php if ($list[0]['profileimg']) { ?>
                    <div class="board-mb-profileimg" style="background-image: url('<?php echo $list[0]['profileimg']; ?>');"></div>
                    <?php } else { ?>
                    <img src="<?php echo MOD_BOARD_THEME_DIR; ?>/images/cmt-name-ico.png" class="ico" />
                    <?php } ?>

                    <p><?php echo $list[0]['writer']; ?></p>
				</span>
				<span class="date"><?php echo $list['datetime']; ?></span>
			</div>
			<div class="comment">
				<?php if ($list[0]['parent_writer']) echo $list[0]['parent_writer']; ?>
				<p><?php echo $list['comment']; // p 내부에는 여백 등 다른 소스코드가 입력되면 안됩니다. ?></p>
			</div>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>

	<!-- no data -->
	<?php if ($total_cnt < 1) { ?>
	<p id="board-nodata">댓글 <?php echo SET_NODATA_MSG; ?></p>
	<?php } ?>

	<!-- 대댓글 입력 & 댓글 수정 Clone -->
	<div id="comm-re-form">
		<img src="<?php echo MOD_BOARD_THEME_DIR; ?>/images/reply-ico.png" class="rep-ico" />
		<?php if ($is_guest_form_show) { ?>
		<div class="gue-writer">
			<input type="text" name="re_writer" title="작성자" placeholder="작성자" maxlength="8" class="inp" />
		</div>
		<?php } ?>

		<fieldset>
			<textarea name="re_comment" title="댓글 내용" placeholder="댓글 내용"></textarea>
			<input type="button" value="작성" class="btn1 re_sbm" />
		</fieldset>
	</div>

</form>
