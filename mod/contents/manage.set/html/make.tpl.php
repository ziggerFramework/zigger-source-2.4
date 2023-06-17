<div id="sub-tit">
    <h2>신규 콘텐츠 생성</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규 페이지 콘텐츠 생성</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

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
                        <input type="text" name="data_key" class="inp" title="콘텐츠 key" />
                        <span class="tbl_sment">영어, 숫자 조합으로 입력<br />최소 3자~최대 15자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>콘텐츠 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="콘텐츠 제목" />
                    </td>
                </tr>
                <tr>
                    <th>PC 콘텐츠 내용</th>
                    <td>
                        <textarea id="html" name="html" title="콘텐츠 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                    </td>
                </tr>
                <tr>
                    <th>모바일 콘텐츠 내용</th>
                    <td>
                        <textarea id="mo_html" name="mo_html" title="모바일 콘텐츠 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('mo_html');</script>
                    </td>
                </tr>
                <tr>
                    <th>모바일 콘텐츠 사용</th>
                    <td>
                        <label><input type="checkbox" name="use_mo_html" value="checked" /> 모바일 콘텐츠 사용</label>
                        <span class="tbl_sment">모바일 콘텐츠를 사용하지 않는 경우 PC 콘텐츠 내용으로 대체</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
