<div id="sub-tit">
    <h2><?php echo $MB['name']; ?>님의 메시지함</h2>
</div>

<?php $this->message_tab(); ?>

<div class="list-head">
	<form name="search-form" id="search-form">
		<legend>검색</legend>
		<fieldset>
			<input type="hidden" name="mode" value="sent" />
			<div class="sltbox">
				<select name="where">
					<option value="mb_id" <?php echo ($where == 'mb_id') ? 'selected' : ''; ?>>받는회원(id)</option>
					<option value="mb_name" <?php echo ($where == 'mb_name') ? 'selected' : ''; ?>>받는회원(이름)</option>
					<option value="article" <?php echo ($where == 'article') ? 'selected' : ''; ?>>내용</option>
				</select>
			</div>
			<input type="text" name="keyword" class="inp keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
			<hr>
			<button type="submit" class="submit btn2 small mo-w100p">검색</button>
		</fieldset>
		
		<a href="?mode=sent" class="reset-btn"><i class="fas fa-times"></i></a>
	</form>

	<div class="right clear">
		<button type="button" class="btn1" data-message-send="" data-message-send-reply="">새로운 메시지 발송</button>
	</div>
</div>

<table class="table listtbl">
    <caption>보낸 메시지함</caption>
	<colgroup>
		<col style="width: 70px;" />
		<col style="width: 250px;" />
        <col style="width: auto;" />
		<col style="width: 120px;" />
		<col style="width: 120px;" />
	</colgroup>
	<thead>
		<tr>
			<th scope="col">No.</th>
			<th scope="col">받는회원</th>
			<th scope="col" class="tal">내용</th>
			<th scope="col">발송일</th>
			<th scope="col">수신확인</th>
		</tr>
	</thead>
	<tbody>

		<?php foreach ($print_arr as $list) { ?>
		<tr>
			<td class="no"><?php echo $list['no']; ?></td>
			<td>
                <?php echo $list['mb_name']; ?> (<?php echo $list['mb_id']; ?>)
            </td>
            <td class="tal">
                <a href="<?php echo $list[0]['view-link']; ?>">
                    <?php echo $list['article']; ?>
                </a>
            </td>
			<td><?php echo $list['regdate']; ?></td>
			<td>
                <?php
                if (!$list['chked']) {
                    echo '읽지않음';
                } else {
                    echo $list['chked'];
                }
                ?>
            </td>
		</tr>
		<?php } ?>

		<?php if (!$print_arr) { ?>
		<tr>
			<td colspan="5"><?php echo SET_NODATA_MSG; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>

<?php echo $pagingprint; ?>
