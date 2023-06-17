<div id="sub-tit">
    <h2>회원 메일 발송</h2>
    <em><i class="fa fa-exclamation-circle"></i>특정 혹은 레벨별 회원 메일 발송</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">메일 정보 입력</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>발송 종류</th>
                    <td>
                        <label><input type="radio" name="type" value="1" checked /> 특정 회원 지정</label>
                        &nbsp;&nbsp;&nbsp;
                        <label><input type="radio" name="type" value="2" /> 수신 범위 지정</label>
                    </td>
                </tr>
                <tr data-type="1" class="hd-tr">
                    <th>수신 회원</th>
                    <td>
                        <input type="text" name="to_mb" class="inp" title="수신 회원" value="<?php echo $mailto; ?>" />
                        <span class="tbl_sment">수신할 회원의 id를 입력</span>
                    </td>
                </tr>
                <tr data-type="2" class="hd-tr" style="display: none;">
                    <th>수신 범위</th>
                    <td>
                        <select name="level_from" class="inp">
                            <?php for($i=1;$i<=9;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==1){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <select name="level_to" class="inp">
                            <?php for($i=1;$i<=9;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==1){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                        <span class="tbl_sment">작은 숫자의 레벨 부터 입력. ex) 1 ~ 9</span>
                    </td>
                </tr>
                <tr>
                    <th>메일 템플릿 선택</th>
                    <td>
                        <select name="tpl" class="inp">
                            <?php echo $tpl_opts; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td>
                        <input type="text" name="subject" class="inp w50p" title="제목" />
                    </td>
                </tr>
                <tr>
                    <th>본문 내용</th>
                    <td>
                        <textarea id="html" name="html" title="본문 내용"></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>메일 발송</button>
            </div>
        </div>
    </form>

</article>
