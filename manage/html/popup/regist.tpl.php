<div id="sub-tit">
    <h2>신규 팝업 생성</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규 반응형 팝업 생성</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">팝업 기본설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>팝업 id</th>
                    <td>
                        <input type="text" name="id" class="inp" title="팝업 id" />
                        <span class="tbl_sment">영어, 숫자 조합으로 입력<br />최소 3자~최대 15자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>팝업 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="팝업 제목" />
                    </td>
                </tr>
                <tr>
                    <th>접속 링크</th>
                    <td>
                        <input type="text" name="link" class="inp w100p" title="접속 링크" />
                        <span class="tbl_sment">팝업 클릭시 위 링크로 이동합니다.</span>
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
                    <th>폭(width)</th>
                    <td>
                        <input type="text" name="width" class="inp w50" title="가로 폭(width)" /> px
                    </td>
                </tr>
                <tr>
                    <th>높이(height)</th>
                    <td>
                        <input type="text" name="height" class="inp w50" title="높이(height)" /> px
                    </td>
                </tr>
                <tr>
                    <th>팝업 top 위치</th>
                    <td>
                        <input type="text" name="pos_top" class="inp w50" title="팝업 top 위치" /> px
                    </td>
                </tr>
                <tr>
                    <th>팝업 left 위치</th>
                    <td>
                        <input type="text" name="pos_left" class="inp w50" title="팝업 left 위치" /> px
                    </td>
                </tr>
                <tr>
                    <th>노출 대상</th>
                    <td>
                        <select name="level_from" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==1){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <select name="level_to" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==10){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                        <span class="tbl_sment">작은 숫자의 레벨 부터 입력. ex) 1 ~ 10</span>
                    </td>
                </tr>
                <tr>
                    <th>노출 기간</th>
                    <td>
                        <input type="text" name="show_from" class="inp" title="팝업 노출 시작일" datepicker />
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <input type="text" name="show_to" class="inp" title="팝업 노출 종료일" datepicker />
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">팝업 내용 설정</th>
                </tr>
                <tr>
                    <th class="tal">PC 팝업 내용</th>
                    <th class="tal">모바일 팝업 내용</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <textarea id="html" name="html" title="PC 팝업 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                    </td>
                    <td>
                        <textarea id="mo_html" name="mo_html" title="모바일 팝업 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('mo_html');</script>
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
