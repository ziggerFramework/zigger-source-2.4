<div id="sub-tit">
    <h2>콘텐츠 정보 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>콘텐츠 정보 확인 및 관리</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="mode" value="mod" />
        <input type="hidden" name="idx" value="<?php echo $write['idx']; ?>" />

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">콘텐츠 내용 설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>콘텐츠 key</th>
                    <td>
                        <strong><?php echo $write['data_key']; ?></strong>
                        <span class="tbl_sment">콘텐츠 key는 변경 불가합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>콘텐츠 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="콘텐츠 제목" value="<?php echo $write['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>PC 콘텐츠 내용</th>
                    <td>
                        <textarea id="html" name="html" title="PC 콘텐츠 내용"><?php echo $write['html']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                    </td>
                </tr>
                <tr>
                    <th>모바일 콘텐츠 내용</th>
                    <td>
                        <textarea id="mo_html" name="mo_html" title="모바일 콘텐츠 내용"><?php echo $write['mo_html']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('mo_html');</script>
                    </td>
                </tr>
                <tr>
                    <th>모바일 콘텐츠 사용</th>
                    <td>
                        <label><input type="checkbox" name="use_mo_html" value="checked" <?php echo $use_mo_html['Y']; ?> /> 모바일 콘텐츠 사용</label>
                        <span class="tbl_sment">모바일 콘텐츠를 사용하지 않는 경우 PC 콘텐츠 내용으로 대체</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="#" class="btn2 mr30" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 삭제 처리 하시겠습니까? => mode:del"><i class="fa fa-trash-alt"></i>콘텐츠 삭제</a>
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
