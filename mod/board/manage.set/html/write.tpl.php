<div id="sub-tit">
    <h2>게시글 작성</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재 게시판에 새로운 게시글 작성</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="request" value="manage" />
    	<input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
    	<input type="hidden" name="wrmode" value="<?php echo $wrmode; ?>" />
    	<input type="hidden" name="read" value="<?php echo $read; ?>" />
    	<input type="hidden" name="page" value="<?php echo $page; ?>" />
    	<input type="hidden" name="where" value="<?php echo $where; ?>" />
    	<input type="hidden" name="keyword" value="<?php echo $keyword; ?>" />
    	<input type="hidden" name="category_ed" value="<?php echo $category; ?>" />
    	<input type="hidden" name="use_html" value="Y" />
    	<input type="hidden" name="thisuri" value="<?php echo $thisuri; ?>" />

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal"><?php echo $write_title; ?></th>
                </tr>
            </thead>
            <tbody>

                <?php if ($is_category_show) { ?>
				<tr>
					<th>카테고리</th>
					<td>
						<select name="category" id="category" class="inp w100">
							<?php echo $category_option; ?>
						</select>
					</td>
				</tr>
				<?php } ?>

                <tr>
					<th>
						옵션
					</th>
					<td>
						<?php echo $opt_notice; ?> &nbsp;&nbsp;&nbsp; <?php echo $opt_secret; ?> &nbsp;&nbsp;&nbsp; <?php echo $opt_return_email; ?>
					</td>
				</tr>

                <?php if ($is_writer_show) { ?>
                <tr>
                    <th>
                        작성자
                    </th>
                    <td>
                        <input type="text" name="writer" title="작성자" value="<?php echo $write['writer']; ?>" maxlength="8" class="inp w100" />
                    </td>
                </tr>
                <?php } ?>

                <?php if ($is_pwd_show) { ?>
                <tr>
                    <th>
                        비밀번호
                    </th>
                    <td>
                        <input type="password" name="password" title="비밀번호" value="<?php echo $write['pwd']; ?>" maxlength="20" class="inp w100" />
                    </td>
                </tr>
                <?php } ?>

                <?php if ($is_email_show) { ?>
                <tr>
                    <th>
                        이메일주소
                    </th>
                    <td>
                        <input type="text" name="email" title="이메일주소" value="<?php echo $write['email']; ?>" class="inp w100" />
                    </td>
                </tr>
                <?php } ?>

                <tr>
                    <th>
                        제목
                    </th>
                    <td class="subject">
                        <input type="text" name="subject" title="제목" class="inp w100p" value="<?php echo $write['subject']; ?>" placeholder="제목을 입력하세요." />
                    </td>
                </tr>

                <tr>
                    <th>
                        내용
                    </th>
                    <td class="article">
                        <textarea name="article" id="article" title="내용" ckeditor><?php echo $write['article']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('article');</script>
                    </td>
                </tr>

                <tr>
					<th>
						작성일
					</th>
					<td>
						<input type="text" name="wdate_date" title="작성일 - 날짜" value="<?php echo $write['wdate_date']; ?>" class="inp w100" placeholder="날짜" datepicker />
                        <select name="wdate_h" class="inp w100">
							<?php
                            for ($i = 0;$i <= 23;$i++) {
                                $j = $i;
                                if (strlen($j) < 2) {
                                    $j = '0'.$j;
                                }
                                $selected = '';
                                if ($j == $write['wdate_h']) {
                                    $selected = 'selected';
                                }
                            ?>
                            <option value="<?php echo $j; ?>" <?php echo $selected; ?>><?php echo $j; ?></option>
							<?php } ?>
						</select> 시
                        <select name="wdate_i" class="inp w100">
                            <?php
                            for ($i = 0;$i <= 59;$i++) {
                                $j = $i;
                                if (strlen($j) < 2) {
                                    $j = '0'.$j;
                                }
                                $selected = '';
                                if ($j == $write['wdate_i']) {
                                    $selected = 'selected';
                                }
                            ?>
                            <option value="<?php echo $j; ?>" <?php echo $selected; ?>><?php echo $j; ?></option>
							<?php } ?>
						</select> 분
                        <select name="wdate_s" class="inp w100">
                            <?php
                            for ($i = 0;$i <= 59;$i++) {
                                $j = $i;
                                if (strlen($j) < 2) {
                                    $j = '0'.$j;
                                }
                                $selected = '';
                                if ($j == $write['wdate_s']) {
                                    $selected = 'selected';
                                }
                            ?>
                            <option value="<?php echo $j; ?>" <?php echo $selected; ?>><?php echo $j; ?></option>
							<?php } ?>
						</select> 초
                        <span class="tbl_sment">날짜를 입력하지 않는 경우 작성일을 자동 기록</span>
					</td>
				</tr>

                <?php if ($is_file_show[1]) { ?>
                <tr>
                    <th>
                        첨부파일
                    </th>
                    <td>
                        <input type="file" name="file1" title="첨부파일1" />
                        <span class="tbl_sment">(<?php echo $print_filesize; ?> 까지 첨부 가능)</span>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($is_filename_show[1]) { ?>
                <tr>
                    <th>
                        첨부된 파일
                    </th>
                    <td>
                        <span class="uploaded"><?php echo $uploaded_file[1]; ?></span>
                        <br />
                        <label class="mt10"><input type="checkbox" name="file1_del" value="checked" />첨부파일 삭제</label>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($is_file_show[2]) { ?>
                <tr>
                    <th>
                        첨부파일2
                    </th>
                    <td>
                        <input type="file" name="file2" title="첨부파일2" />
                        <span class="tbl_sment">(<?php echo $print_filesize; ?> 까지 첨부 가능)</span>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($is_filename_show[2]) { ?>
                <tr>
                    <th>
                        첨부된 파일2
                    </th>
                    <td>
                        <span class="uploaded"><?php echo $uploaded_file[2]; ?></span>
                        <br />
                        <label class="mt10"><input type="checkbox" name="file2_del" value="checked">삭제</label>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="btn-wrap">
			<?php echo $cancel_btn; ?>
			<button type="submit" class="btn1"><i class="fa fa-check"></i> 작성 완료</button>
    	</div>
    </form>

</article>
