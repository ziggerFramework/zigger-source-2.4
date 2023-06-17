<div id="signin">
    <form <?php echo $this->form(); ?>>

        <h4>회원 로그인 정보 찾기</h4>
        <span class="log-noti">
            아직 회원이 아니신가요? <a href="<?php echo PH_DIR; ?>/sign/signup">지금 바로 회원으로 가입</a>
        </span>

        <fieldset class="inp-wrap">
            <label for="email">회원 E-mail <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="text" name="email" id="email" title="회원 E-mail" class="inp" required />
            <span class="tbltxt">
                · 회원가입시 등록한 E-mail 입력 <br />
                · E-mail로 회원 로그인 ID 와 임시 Password 가 발송됩니다.
            </span>

            <button type="submit" class="btn1 w100p mt15">회원 정보 발송</button>
        </fieldset>

    </form>
</div>
