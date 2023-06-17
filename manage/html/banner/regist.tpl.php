<div id="sub-tit">
    <h2>신규 배너 생성</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규 반응형 배너 생성</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">배너 기본설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>배너 key</th>
                    <td>
                        <input type="text" name="key" class="inp" title="배너 key" />
                        <span class="tbl_sment">타 배너와 중복된 key를 입력하여 다중노출 가능<br />영어, 숫자 조합으로 입력<br />최소 3자~최대 15자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>배너 순서</th>
                    <td>
                        <input type="text" name="zindex" class="inp" title="배너 순서" placeholder="1" />
                        <span class="tbl_sment">타 배너와 key가 같은 경우 우선 순위 지정<br />숫자가 작을 수록 먼저 노출</span>
                    </td>
                </tr>
                <tr>
                    <th>배너 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="배너 제목" />
                    </td>
                </tr>
                <tr>
                    <th>배너 링크</th>
                    <td>
                        <input type="text" name="link" class="inp w100p" title="배너 링크" />
                        <span class="tbl_sment">배너 클릭시 위 링크로 이동합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>링크 target</th>
                    <td>
                        <select name="link_target" class="inp">
                            <option value="_self">현재창 (_self)</option>
                            <option value="_blank">새창 (_blank)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>PC 배너 이미지</th>
                    <td>
                        <input type="file" name="pc_img" />
                    </td>
                </tr>
                <tr>
                    <th>모바일 배너 이미지</th>
                    <td>
                        <input type="file" name="mo_img" />
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
