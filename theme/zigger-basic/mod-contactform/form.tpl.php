<div class="tblform">
    <form <?php echo $this->form(); ?>>

        <h4>고객 문의</h4>

        <fieldset>
            <table class="table_wrt">
                <colgroup>
                    <col style="width: 150px;" />
                    <col style="width: auto;" />
                </colgroup>
                <tbody>

                    <?php
                    //회원인 경우 회원 이름 출력
                    if (IS_MEMBER) {
                    ?>

                    <tr>
                        <th>회원 이름</th>
                        <td>
                            <input type="hidden" name="name" value="<?php echo $MB['name']; ?>" />
                            <?php echo $MB['name']; ?>
                        </td>
                    </tr>

                    <?php
                    //회원이 아닌 경우 이름 입력 input 출력
                    } else {
                    ?>

                    <tr>
                        <th>문의자 이름</th>
                        <td>
                            <label for="name" class="sound_only_ele">문의자 이름 <strong>필수 입력</strong></label>
                            <input type="text" name="name" id="name" title="문의자 이름" class="inp w50" required />
                        </td>
                    </tr>

                    <?php } ?>

                    <tr>
                        <th>이메일</th>
                        <td>
                            <label for="email" class="sound_only_ele">이메일 - 문의에 대한 답변은 이메일로 발송 됩니다. <strong>필수 입력</strong></label>
                            <input type="text" name="email" id="email" title="이메일" value="<?php echo $MB['email']; ?>" class="inp w100" required />
                            <ul class="tbltxt">
                                <li>문의에 대한 답변은 이메일로 발송 됩니다.</li>
                                <li>정확하게 입력해 주시기 바랍니다.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th>연락처</th>
                        <td>
                            <label for="phone" class="sound_only_ele">연락처 - 하이픈(-) 없이 입력 하세요. <strong>필수 입력</strong></label>
                            <input type="text" name="phone" id="phone" title="연락처" value="<?php echo $MB['phone']; ?>" class="inp w100" required />
                            <ul class="tbltxt">
                                <li>하이픈(-) 없이 입력 하세요.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th>문의 내용</th>
                        <td>
                            <label for="article" class="sound_only_ele">문의 내용 <strong>필수 입력</strong></label>
                            <textarea name="article" id="article" title="문의 내용" required></textarea>
                        </td>
                    </tr>

                    <?php
                    //회원이 아닌 경우 스팸방지 코드 보임
                    if (!IS_MEMBER) {
                    ?>

                    <tr>
                        <th>Captcha</th>
                        <td>
                            <?php echo $captcha; ?>
                            <label for="captcha" class="sound_only_ele">Captcha <strong>필수 입력</strong></label>
                        </td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>
        </fieldset>

        <div class="btn-wrap">
            <a href="<?php echo PH_DOMAIN; ?>" class="btn2">취소</a>
            <button type="submit" class="btn1">문의하기</button>
        </div>

    </form>
</div>
