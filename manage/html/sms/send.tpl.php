<div id="sub-tit">
    <h2>비회원 SMS 발송</h2>
    <em><i class="fa fa-exclamation-circle"></i>비회원 휴대전화번호로 SMS 발송</em>
</div>

<!-- article -->
<article>
    <?php if ($is_show_wait) { ?>
    <p class="article-wait">
        <i class="fa fa-info-circle"></i>
        SMS 발송 기능이 비활성화 되어 있습니다.
        <em>'<a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/plugins">기본 관리도구 > 플러그인 및 기능 설정</a>' 에서 설정 후 이용해 주시길 바랍니다.</em>
    </p>
    <?php } ?>

    <form <?php echo $this->form(); ?>>
        <input type="hidden" name="type" value="3" />
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">SMS 발송 설정</th>
                </tr>
            </thead>
            <tbody>
                <tr data-type="1" class="hd-tr">
                    <th>수신 번호</th>
                    <td>
                        <input type="text" name="to_phone" class="inp w50p" title="수신 번호" />
                        <span class="tbl_sment">수신할 휴대전화번호 입력. <br />ex) 01012345678</span>
                        <span class="tbl_sment mt5">여러 번호로 다건 발송하는 경우 콤마(,)로 구분. <br />ex) 01012345678,01012345679</span>
                    </td>
                </tr>
                <tr data-type="2" class="hd-tr" style="display: none;">
                    <th>수신 범위</th>
                    <td>
                        <select name="level_from" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==1){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <select name="level_to" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==1){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                        <span class="tbl_sment">작은 숫자의 레벨 부터 입력. ex) 1 ~ 10</span>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td>
                        <input type="text" name="subject" class="inp w50p" title="제목" placeholder="optional" />
                    </td>
                </tr>
                <tr>
                    <th>문자메시지 내용</th>
                    <td>
                        <textarea id="memo" name="memo" title="문자메시지 내용" style="width: 50%;"></textarea>
                        <span class="tbl_sment print_byte">
                            <strong style="color: #000;"><strong>0</strong>byte</strong> / 80byte 이하 SMS발송
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>이미지 파일 첨부</th>
                    <td>
                        <input type="file" name="image" title="이미지 파일" />
                        <span class="tbl_sment">문자메시지에 이미지 파일이 첨부되는 경우 MMS 발송</span>
                        <span class="tbl_sment mt0">jpg, jpeg 유형의 파일만 첨부 가능</span>
                    </td>
                </tr>
                <tr>
                    <th>예약 발송</th>
                    <td>
                        <label class="resv-btn"><input type="checkbox" name="use_resv" value="checked" /> 예약발송 설정</label>

                        <div class="resv-wrap mt10">
                            <input type="text" name="resv_date" class="inp" title="예약발송일" value="<?php echo date('Y-m-d'); ?>" style="width: 150px;" datepicker readonly />
                            <select name="resv_hour" class="inp" style="width: 100px;">
                                <?php for ($i=0; $i<=23; $i++) { ?>
                                <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                                <?php } ?>
                            </select> 시
                            <select name="resv_min" class="inp" style="width: 100px;">
                                <?php for ($i=0; $i<=59; $i++) { ?>
                                <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                                <?php } ?>
                            </select> 분
                        </div>
                        <span class="tbl_sment">특정 시간에 예약발송 하는 경우 설정</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i><strong>SMS</strong> 발송</button>
            </div>
        </div>
    </form>

</article>
