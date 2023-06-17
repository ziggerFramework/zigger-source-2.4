<div id="signin">
    <form <?php echo $this->form(); ?>>

        <h4>회원으로 가입하고 시작하세요.</h4>
        <span class="log-noti">
            이미 회원이신가요? <a href="<?php echo PH_DIR; ?>/sign/signin">지금 바로 회원 로그인</a>
        </span>

        <?php if ($show_sns_ka == 'Y' || $show_sns_nv == 'Y') { ?>
        <fieldset class="snsbox">
            <h5>SNS Sign up</h5>
            <ul>
                <?php if ($show_sns_ka == 'Y') { ?>
                <li><a id="kakao-login" href="<?php echo PH_PLUGIN_DIR; ?>/snslogin/getlogin.php?get_sns=kakao"><img src="<?php echo PH_THEME_DIR; ?>/layout/images/login-sns-ico-k.jpg">Log in with Kakao</a></li>
                <?php } ?>
                <?php if ($show_sns_nv == 'Y') { ?>
                <li><a id="naver-login" href="<?php echo PH_PLUGIN_DIR; ?>/snslogin/getlogin.php?get_sns=naver"><img src="<?php echo PH_THEME_DIR; ?>/layout/images/login-sns-ico-n.jpg">Log in with Naver</a></li>
                <?php } ?>
            </ul>
        </fieldset>
        <p class="or">OR</p>
        <?php } ?>

        <fieldset class="inp-wrap">

            <label for="id">User ID <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="text" name="id" id="id" title="User ID" class="inp" data-validt-action="/sign/Signup-check-id" data-validt-event="keyup" data-validt-group="id" required />
            <span class="validt" data-validt-group="id"></span>
            <span class="tbltxt">
                · 영어, 숫자 조합으로 입력<br />
                · 최소 5자~최대 30자 까지 입력
            </span>

            <label for="email">E-mail <p class="sound_only_ele">회원 E-mail<strong>필수 입력</strong></p></label>
            <input type="text" name="email" id="email" title="E-mail" class="inp" data-validt-action="/sign/Signup-check-email" data-validt-event="keyup" data-validt-group="email" required />
            <span class="validt" data-validt-group="email"></span>
            <span class="tbltxt">
                · 회원 로그인 정보 분실시 입력한 이메일로 조회 가능
            </span>

            <label for="pwd">Password <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="password" name="pwd" id="pwd" title="Password" class="inp mb5" data-validt-action="/sign/Signup-check-password" data-validt-event="keyup" data-validt-group="pwd" required />
            <span class="validt" data-validt-group="pwd"></span>
            <span class="tbltxt">
                · 최소 5자~최대 50자 까지 입력
            </span>

            <label for="pwd2">Password 확인 <p class="sound_only_ele"><strong>필수 입력</strong></p></label>
            <input type="password" name="pwd2" id="pwd2" title="Password 확인" class="inp" required />

            <label for="name">사용자 이름</label>
            <input type="text" name="name" id="name" title="사용자 이름" class="inp" required />

            <?php if ($siteconf['use_mb_gender'] != 'N') { ?>
            <div class="labelWrap">
                <label><input type="radio" name="gender" id="gender" title="남자" alt="남자" value="M" checked />남자</label>
                <label><input type="radio" name="gender" id="gender" title="여자" alt="여자" value="F" />여자</label>
            </div>
            <?php } ?>

            <?php if ($siteconf['use_mb_phone'] != 'N') { ?>
            <div id="get-phone-check-wrap">

                <label for="phone">휴대전화 <p class="sound_only_ele">하이픈(-) 없이 숫자만 입력 <?php if ($siteconf['use_mb_phone'] == 'Y'){ ?><strong>필수 입력</strong><?php } ?></p></label>
                <input type="text" name="phone" id="phone" title="휴대전화" class="inp w100" <?php if ($siteconf['use_mb_phone'] == 'Y') echo 'required'; ?> />
                <?php if ($siteconf['use_phonechk'] == 'Y' && $siteconf['use_sms'] == 'Y') { ?>
                <button type="button" class="btn2 small mb5 send-sms-code">SMS 인증코드 발송</button>
                <?php } ?>
                <span class="tbltxt">
                    · 하이픈(-) 없이 숫자만 입력
                </span>

                <div id="confirm-sms-code-wrap" style="display: none;">
                    <label for="phone_code" class="sound_only_ele">휴대전화 인증코드</label>
                    <input type="text" name="phone_code" id="phone_code" title="휴대전화 인증코드" placeholder="휴대전화 인증코드 입력" class="inp w100" />
                    <button type="button" class="btn2 small mb5 confirm-sms-code">인증코드 입력 완료</button>
                    <span class="tbltxt">
                        · SMS 발송된 6자리 인증코드 입력
                    </span>
                </div>

            </div>
            <?php } ?>

            <?php if ($siteconf['use_mb_telephone'] != 'N') { ?>
            <label for="telephone">전화번호 <p class="sound_only_ele">하이픈(-) 없이 숫자만 입력 <?php if ($siteconf['use_mb_telephone'] == 'Y'){ ?><strong>필수 입력</strong><?php } ?></p></label>
            <input type="text" name="telephone" id="telephone" title="전화번호" class="inp w100" <?php if ($siteconf['use_mb_telephone'] == 'Y') echo 'required'; ?> />
            <span class="tbltxt">
                · 하이픈(-) 없이 숫자만 입력
            </span>
            <?php } ?>

            <?php if ($siteconf['use_mb_address'] != 'N') { ?>
            <div id="get-address-search-wrap">

                <label for="address1">주소 <?php if ($siteconf['use_mb_address'] == 'Y'){ ?><strong class="sound_only_ele">필수 입력</strong><?php } ?></label>
                <button type="button" class="btn2 small mb5 search-address-btn">주소검색</button>
                <input type="text" name="address1" id="address1" title="주소 - 우편번호" placeholder="우편번호" class="inp w50" <?php if ($siteconf['use_mb_address'] == 'Y') echo 'required'; ?> />

                <label for="address2" class="sound_only_ele">주소 (기본주소) <?php if ($siteconf['use_mb_address'] == 'Y'){ ?><strong class="sound_only_ele">필수 입력</strong><?php } ?></label>
                <input type="text" name="address2" id="address2" title="주소 - 기본주소" placeholder="기본주소" class="inp w100" <?php if ($siteconf['use_mb_address'] == 'Y') echo 'required'; ?>/>

                <label for="address3" class="sound_only_ele">주소 (상세주소)</label>
                <input type="text" name="address3" id="address3" title="주소 - 상세주소" placeholder="상세주소" class="inp w100" />

            </div>
            <?php } ?>

            <label class="tar mb15">
                <input type="checkbox" name="policy" value="checked" alt="서비스이용약관 및 개인정보처리방침에 동의합니다." />
                회원가입을 위해 <a href="<?php echo PH_DIR; ?>/doc/terms-of-service" class="forgot" target="_blank">서비스이용약관</a> 및 <a href="<?php echo PH_DIR; ?>/doc/privacy-policy" class="forgot" target="_blank">개인정보처리방침</a> 에 동의합니다.
            </label>

            <button type="submit" class="btn1 w100p mt10">회원가입</button>
        </fieldset>

    </form>
</div>
