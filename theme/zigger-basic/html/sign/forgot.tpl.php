<div id="signin">
    <form <?php echo $this->form(); ?>>

        <h4>로그인 정보를 찾으세요.</h4>

        <fieldset class="inp-wrap">
            <label for="email">회원 E-mail <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="text" name="email" id="email" title="회원 E-mail" class="inp" required />
            <ul class="tbltxt">
                <li>회원가입시 등록한 E-mail 입력</li>
                <li>E-mail로 회원 로그인 ID 와 임시 Password 가 발송됩니다.</li>
            </ul>

            <button type="submit" class="btn1 w100p mt15">회원 정보 발송</button>
        </fieldset>

    </form>
</div>
