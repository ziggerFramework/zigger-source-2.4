<div id="signin">
    <form <?php echo $this->form(); ?>>
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />


        <h4><?php echo $this->layout->logo_title(); ?>에 로그인하세요.</h4>
        <span class="log-noti">
            아직 회원이 아니신가요? <a href="<?php echo PH_DIR; ?>/sign/signup">지금 바로 회원으로 가입</a>
        </span>

        <?php if ($show_sns_ka == 'Y' || $show_sns_nv == 'Y') { ?>
        <fieldset class="snsbox">
            <h5>SNS Log in</h5>
            <ul>
                <?php if ($show_sns_ka == 'Y') { ?>
                <li><a id="kakao-login" href="<?php echo PH_PLUGIN_DIR; ?>/snslogin/getlogin.php?get_sns=kakao&redirect=<?php echo $redirect; ?>"><img src="<?php echo PH_THEME_DIR; ?>/layout/images/login-sns-ico-k.jpg">Log in with Kakao</a></li>
                <?php } ?>
                <?php if ($show_sns_nv == 'Y') { ?>
                <li><a id="naver-login" href="<?php echo PH_PLUGIN_DIR; ?>/snslogin/getlogin.php?get_sns=naver&redirect=<?php echo $redirect; ?>"><img src="<?php echo PH_THEME_DIR; ?>/layout/images/login-sns-ico-n.jpg">Log in with Naver</a></li>
                <?php } ?>
            </ul>
        </fieldset>
        <p class="or">OR</p>
        <?php } ?>

        <fieldset class="inp-wrap">
            <label for="id">User ID <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="text" name="id" id="id" title="User ID" class="inp" value="<?php echo $id_val; ?>" required />

            <label for="pwd">Password <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="password" name="pwd" id="pwd" title="Password" class="inp" required />

            <div class="tar mb15">
                <label><input type="checkbox" name="save" value="checked" <?php echo $save_checked; ?> /> 회원 아이디를 저장 하겠습니다.</label>
                <a href="<?php echo PH_DIR; ?>/sign/forgot" class="forgot">로그인 정보를 분실했어요.</a>
            </div>
            <button type="submit" class="btn1 w100p">로그인</button>
        </fieldset>

    </form>
</div>
