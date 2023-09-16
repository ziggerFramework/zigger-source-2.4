<!-- top source code -->
<div id="mod_board_top_source"><?php echo $top_source; ?></div>

<form <?php echo $this->form(); ?>>
	<input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
	<input type="hidden" name="wrmode" value="<?php echo $wrmode; ?>" />
	<input type="hidden" name="read" value="<?php echo $read; ?>" />
	<input type="hidden" name="page" value="<?php echo $page; ?>" />
	<input type="hidden" name="where" value="<?php echo $where; ?>" />
	<input type="hidden" name="keyword" value="<?php echo $keyword; ?>" />
	<input type="hidden" name="category_ed" value="<?php echo $category; ?>" />
	<input type="hidden" name="use_html" value="Y" />
	<input type="hidden" name="thisuri" value="<?php echo $thisuri; ?>" />
	<div id="board-write">
		<h3><?php echo $write_title; ?></h3>

        <table>
            <caption>게시글 작성하기</caption>
			<colgroup>
				<col style="width: 100px;" />
				<col style="width: auto;" />
			</colgroup>
			<tbody>

				<?php if ($is_category_show) { ?>
				<tr>
					<th scope="row">카테고리</th>
					<td>
                        <label for="category" class="sound_only_ele">카테고리</label>
						<select name="category" id="category" class="inp w100">
							<?php echo $category_option; ?>
						</select>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th scope="row">
						옵션
					</th>
					<td>
						<?php echo $opt_notice; ?> <?php echo $opt_secret; ?> <?php echo $opt_return_email; ?>
					</td>
				</tr>

				<?php if ($is_writer_show) { ?>
				<tr>
					<th scope="row">
						작성자
					</th>
					<td>
                        <label for="writer" class="sound_only_ele">작성자 <strong>필수 입력</strong></label>
						<input type="text" name="writer" id="writer" title="작성자" value="<?php echo $write['writer']; ?>" maxlength="8" class="inp w100" required />
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_pwd_show) { ?>
				<tr>
					<th scope="row">
						비밀번호
					</th>
					<td>
                        <label for="password" class="sound_only_ele">비밀번호 <strong>필수 입력</strong></label>
						<input type="password" name="password" id="password" title="비밀번호" value="<?php echo $write['pwd']; ?>" maxlength="20" class="inp w100" required />
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_email_show) { ?>
				<tr>
					<th scope="row">
						이메일주소
					</th>
					<td>
                        <label for="email" class="sound_only_ele">이메일주소</label>
						<input type="text" name="email" id="email" title="이메일주소" value="<?php echo $write['email']; ?>" class="inp w100" />
					</td>
				</tr>
				<?php } ?>

				<tr>
					<td colspan="2" class="subject">
                        <label for="subject" class="sound_only_ele">제목</label>
						<input type="text" name="subject" id="subject" title="제목" class="inp wfull" value="<?php echo $write['subject']; ?>" maxlength="100" placeholder="제목을 입력하세요." required />
					</td>
				</tr>

				<tr>
					<td colspan="2" class="article">
                        <label for="article" class="sound_only_ele">내용</label>
						<textarea name="article" id="article" title="내용" ckeditor><?php echo $write['article']; ?></textarea>
						<script type="text/javascript">CKEDITOR.replace('article');</script>
					</td>
				</tr>

				<?php if ($is_file_show[1]) { ?>
				<tr>
					<th scope="row">
						첨부파일
					</th>
					<td>
                        <label for="file1" class="sound_only_ele">첨부파일1</label>
						<input type="file" name="file1" id="file1" title="첨부파일1" /><span class="bytetxt">(<?php echo $print_filesize; ?> 까지 첨부 가능)</span>
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_filename_show[1]) { ?>
				<tr>
					<th scope="row">
						첨부된 파일
					</th>
					<td>
						<span class="uploaded"><?php echo $uploaded_file[1]; ?></span>
						<label><input type="checkbox" name="file1_del" value="checked" alt="첨부파일1 삭제" />삭제</label>
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_file_show[2]) { ?>
				<tr>
					<th scope="row">
						첨부파일2
					</th>
					<td>
                        <label for="file2" class="sound_only_ele">첨부파일2</label>
						<input type="file" name="file2" id="file2" title="첨부파일2" /><span class="bytetxt">(<?php echo $print_filesize; ?> 까지 첨부 가능)</span>
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_filename_show[2]) { ?>
				<tr>
					<th scope="row">
						첨부된 파일2
					</th>
					<td>
						<span class="uploaded"><?php echo $uploaded_file[2]; ?></span>
						<label><input type="checkbox" name="file2_del" value="checked" alt="첨부파일2 삭제">삭제</label>
					</td>
				</tr>
				<?php } ?>

				<?php if ($is_captcha_show) { ?>
				<tr>
					<th scope="row">스팸방지</th>
					<td>
						<?php echo $captcha; ?>
                        <label for="captcha" class="sound_only_ele">스팸방지 코드 입력</label>
					</td>
				</tr>
				<?php } ?>

			</tbody>
		</table>
	</div>

	<!-- button -->
	<div class="btn-wrap">
		<div class="left">
			<?php echo $cancel_btn; ?>
		</div>
		<div class="right">
			<button type="submit" class="btn1"><i class="fa fa-check"></i> 작성 완료</button>
		</div>
	</div>

</form>

<!-- bottom source code -->
<div id="mod_board_bottom_source"><?php echo $bottom_source; ?></div>
