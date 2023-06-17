<div id="sub-tit">
    <h2>온라인 문의 확인</h2>
    <em><i class="fa fa-exclamation-circle"></i>온라인 문의 내역 확인 및 답변</em>
</div>

<!-- article -->
<article>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="mode" value="rep" />
        <input type="hidden" name="idx" value="<?php echo $view['idx']; ?>" />

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">온라인 문의 내용</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>문의자</th>
                    <td>
                        <strong><?php echo $print_name; ?></strong>
                    </td>
                </tr>
                <tr>
                    <th>문의자 이메일</th>
                    <td>
                        <?php echo $view['email']; ?>
                        <span class="tbl_sment">위 이메일로 답변 메일이 발송됩니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>문의자 연락처</th>
                    <td>
                        <?php echo $view['phone']; ?>
                    </td>
                </tr>
                <tr>
                    <th>문의 작성일</th>
                    <td>
                        <?php echo $view['regdate']; ?>
                    </td>
                </tr>
                <tr>
                    <th>답변 여부</th>
                    <td>
                        <?php echo $print_reply; ?>
                    </td>
                </tr>
                <tr>
                    <th>문의 내용</th>
                    <td>
                        <?php echo $view['article']; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if ($is_reply_show) { ?>
        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">등록된 답변</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>답변 시간</th>
                    <td class="nostyle">
                        <?php echo $print_reply; ?>
                    </td>
                </tr>
                <tr>
                    <th>답변 내용</th>
                    <td class="nostyle">
                        <?php echo $repview['article']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php } ?>

        <?php if (!$is_reply_show) { ?>
        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">답변 발송</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>답변 내용</th>
                    <td>
                        <textarea id="article" name="article" title="답변 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('article');</script>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php } ?>

        <div class="btn-wrap">
            <div class="center">
                <a href="#" class="btn2 mr30" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 삭제 처리 하시겠습니까? => mode:del"><i class="fa fa-trash-alt"></i>문의 내역 삭제</a>
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <?php if ($is_reply_btn_show) { ?>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>답변 발송</button>
                <?php } ?>
            </div>
        </div>
    </form>

</article>
