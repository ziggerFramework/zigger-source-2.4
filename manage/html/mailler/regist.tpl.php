<div id="sub-tit">
    <h2>신규 메일 템플릿 생성</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규 메일 템플릿 생성</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>


        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">메일 템플릿 기본설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>템플릿 type</th>
                    <td>
                        <input type="text" name="type" class="inp" title="템플릿 type" />
                        <span class="tbl_sment">영어, 숫자 조합으로 입력<br />최소 3자~최대 15자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>템플릿 설명</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="템플릿 설명" />
                    </td>
                </tr>
                <tr>
                    <th>템플릿 내용</th>
                    <td>
                        <textarea id="html" name="html" title="본문 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                        <span class="tbl_sment">
                            사용자 정의 템플릿을 생성합니다. 사용자 정의 템플릿은 아래와 같은 치환자를 제공합니다.<br />
                            사이트명: <strong>{{site_title}}</strong>
                        </span>
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
