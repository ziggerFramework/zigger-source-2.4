<div id="sub-tit">
    <h2>플러그인 및 기능 설정</h2>
    <em><i class="fa fa-exclamation-circle"></i>zigger 내장 플러그인 및 기능 활성화</em>
</div>

<!-- article -->
<article>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <?php echo $print_target[0]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">Google reCaptcha 연동</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Google reCAPTCHA v2</th>
                    <td>
                        <table class="table2">
                            <tbody>
                                <tr>
                                    <th>사용 유무</th>
                                    <td>
                                        <label class="mr10"><input type="radio" name="use_recaptcha" value="Y" <?php echo $use_recaptcha['Y']; ?> /> 사용</label>
                                        <label><input type="radio" name="use_recaptcha" value="N" <?php echo $use_recaptcha['N']; ?> /> 사용안함</label>
                                        <span class="tbl_sment">
                                            기본 'Captcha' 대신 Google 'reCAPTCHA'를 사용하려면 체크<br />
                                            reCAPTCHA는 공식 웹사이트에서 KEY 발급 후 사용 가능
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Site key</th>
                                    <td>
                                        <input type="text" name="recaptcha_key1" class="inp w50p" title="reCAPTCHA Site key" value="<?php echo $write['recaptcha_key1']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Secret key</th>
                                    <td>
                                        <input type="text" name="recaptcha_key2" class="inp w50p" title="reCAPTCHA Secret key" value="<?php echo $write['recaptcha_key2']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Google reCAPTCHA 신청</th>
                                    <td>
                                        <a href="https://www.google.com/recaptcha/about/" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> Google reCAPTCHA 공식 사이트 이동</a>
                                        <span class="tbl_sment">
                                            Google reCAPTCHA 등록을 통해 Site key 및 Secret key 발급 가능
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[1]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">SNS 로그인 API 관리</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>카카오 로그인</th>
                    <td>
                        <table class="table2">
                            <tbody>
                                <tr>
                                    <th>사용 유무</th>
                                    <td>
                                        <label class="mr10"><input type="radio" name="use_sns_ka" value="Y" <?php echo $use_sns_ka['Y']; ?> /> 사용</label>
                                        <label><input type="radio" name="use_sns_ka" value="N" <?php echo $use_sns_ka['N']; ?> /> 사용안함</label>
                                        <span class="tbl_sment">
                                            로그인 화면에서 카카오 로그인 기능을 사용하려면 체크
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Client ID</th>
                                    <td>
                                        <input type="text" name="sns_ka_key1" class="inp w50p" title="카카오 Client ID" value="<?php echo $write['sns_ka_key1']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Client Secret</th>
                                    <td>
                                        <input type="text" name="sns_ka_key2" class="inp w50p" title="카카오 Client Secret" value="<?php echo $write['sns_ka_key2']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Callback URI</th>
                                    <td><?php echo PH_DOMAIN.'/plugin/snslogin/kakaologin.php'; ?></td>
                                </tr>
                                <tr>
                                    <th>카카오 로그인 신청</th>
                                    <td>
                                        <a href="https://developers.kakao.com/" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> 카카오 로그인 공식 사이트 이동</a>
                                        <span class="tbl_sment">
                                            카카오 로그인 등록을 통해 Client ID 및 Client Secret 발급 가능
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>네이버 로그인</th>
                    <td>
                        <table class="table2">
                            <tbody>
                                <tr>
                                    <th>사용 유무</th>
                                    <td>
                                        <label class="mr10"><input type="radio" name="use_sns_nv" value="Y" <?php echo $use_sns_nv['Y']; ?> /> 사용</label>
                                        <label><input type="radio" name="use_sns_nv" value="N" <?php echo $use_sns_nv['N']; ?> /> 사용안함</label>
                                        <span class="tbl_sment">
                                            로그인 화면에서 네이버 로그인 기능을 사용하려면 체크
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Client ID</th>
                                    <td>
                                        <input type="text" name="sns_nv_key1" class="inp w50p" title="네이버 Client ID" value="<?php echo $write['sns_nv_key1']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Client Secret</th>
                                    <td>
                                        <input type="text" name="sns_nv_key2" class="inp w50p" title="네이버 Client Secret" value="<?php echo $write['sns_nv_key2']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Callback URI</th>
                                    <td><?php echo PH_DOMAIN.'/plugin/snslogin/naverlogin.php'; ?></td>
                                </tr>
                                <tr>
                                    <th>네이버 로그인 신청</th>
                                    <td>
                                        <a href="https://developers.naver.com/" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> 네이버 로그인 공식 사이트 이동</a>
                                        <span class="tbl_sment">
                                            네이버 로그인 등록을 통해 Client ID 및 Client Secret 발급 가능
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[2]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">RSS 발행 설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>RSS 발행 사용</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_rss" value="Y" <?php echo $use_rss['Y']; ?> /> 발행</label>
                        <label><input type="radio" name="use_rss" value="N" <?php echo $use_rss['N']; ?> /> 발행안함</label>
                        <span class="tbl_sment">
                            RSS 주소 : <a href="<?php echo PH_DOMAIN; ?>/rss" target="_blank"><?php echo PH_DOMAIN; ?>/rss</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>RSS 발행 구문</th>
                    <td>
                        <textarea name="rss_boards" title="rss 발행 구문" style="height: 300px;"><?php echo $write['rss_boards']; ?></textarea>
                        <span class="tbl_sment">
                            <strong>board_id</strong> : 게시판의 고유 id (게시판 모듈 참고)<br />
                            <strong>title</strong> : RSS에서 노출될 게시판 제목<br />
                            <strong>link</strong> : RSS에서 클릭시 이동할 게시판 페이지 URL
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[3]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">외부 SMTP(메일서버) 연동</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>외부 메일서버(SMTP) 사용</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_smtp" value="Y" <?php echo $use_smtp['Y']; ?> /> 사용</label>
                        <label><input type="radio" name="use_smtp" value="N" <?php echo $use_smtp['N']; ?> /> 사용안함</label>
                        <span class="tbl_sment">
                            zigger에서 발송되는 이메일을 로컬 메일서버가 아닌 외부 메일서버(SMTP)를 통해 발송
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>SMTP Server</th>
                    <td>
                        <input type="text" name="smtp_server" class="inp" title="SMTP Server" value="<?php echo $write['smtp_server']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Port</th>
                    <td>
                        <input type="text" name="smtp_port" class="inp w50" title="SMTP Port" value="<?php echo $write['smtp_port']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>ID</th>
                    <td>
                        <input type="text" name="smtp_id" class="inp" title="SMTP ID" value="<?php echo $write['smtp_id']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <input type="password" name="smtp_pwd" class="inp" title="SMTP Password" value="<?php echo $write['smtp_pwd']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>AWS SES 신청</th>
                    <td>
                        <a href="https://aws.amazon.com/ko/ses/" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> AWS SES 공식 사이트 이동</a>
                        <span class="tbl_sment">
                            AWS SES 서비스를 신청하여 발급 받은 SMTP를 zigger와 연동하여 메일 발송 가능<br />
                            Amazon Web Services SES(Simple Email Service)
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[4]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">Object Storage(AWS S3) 연동</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>사용 유무</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_s3" value="Y" <?php echo $use_s3['Y']; ?> /> 사용</label>
                        <label><input type="radio" name="use_s3" value="N" <?php echo $use_s3['N']; ?> /> 사용안함</label>
                        <span class="tbl_sment">
                            zigger에서 업로드 되는 모든 첨부파일을 원격 Object Storage(AWS S3)로 분산 업로드<br />
                            AWS (Amazon) S3 혹은 GCP (Google), Azure (Microsoft), NCP (Naver)등 S3와 호환되는 Object Storage를 zigger와 연동 가능
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Endpoint 유형</th>
                    <td>
                        <label class="mr10"><input type="radio" name="s3_path_style" value="N" <?php echo $s3_path_style['N']; ?> /> 가상 호스팅 방식</label>
                        <label><input type="radio" name="s3_path_style" value="Y" <?php echo $s3_path_style['Y']; ?> /> 경로 방식</label>
                        <span class="tbl_sment">
                            S3 Endpoint 가 가상호스팅 방식(https://버킷명.s3.amazonaws.com/파일명)인 경우 '가상 호스팅 방식'을 선택<br />
                            경로 방식(https://s3.us-west-2.amazonaws.com/버킷명/파일명.jpg)인 경우 '경로 방식'을 선택
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Endpoint</th>
                    <td>
                        <input type="text" name="s3_key1" class="inp w50p" title="Object Storage Endpoint" value="<?php echo $write['s3_key1']; ?>" placeholder="https://s3.ap-northeast-2.amazonaws.com" />
                    </td>
                </tr>
                <tr>
                    <th>Bucket</th>
                    <td>
                        <input type="text" name="s3_key2" class="inp w50p" title="Object Storage Bucket" value="<?php echo $write['s3_key2']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Access key</th>
                    <td>
                        <input type="text" name="s3_key3" class="inp w50p" title="Object Storage Client key" value="<?php echo $write['s3_key3']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Secret key</th>
                    <td>
                        <input type="text" name="s3_key4" class="inp w50p" title="Object Storage Secret key" value="<?php echo $write['s3_key4']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Region</th>
                    <td>
                        <input type="text" name="s3_key5" class="inp w33p" title="Object Storage Region" value="<?php echo $write['s3_key5']; ?>" placeholder="ap-northeast-2" />
                    </td>
                </tr>
                <tr>
                    <th>AWS S3 신청</th>
                    <td>
                        <a href="https://aws.amazon.com/ko/s3/" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> AWS S3 공식 사이트 이동</a>
                        <span class="tbl_sment">
                            zigger와 연동을 위한 AWS S3 Client key 및 Secret key 발급은 공식사이트를 참고<br />
                            Amazon Web Services S3(Object Storage)
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[5]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">SMS 문자발송(NCP SENS) 연동</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>사용 유무</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_sms" value="Y" <?php echo $use_sms['Y']; ?> /> 사용</label>
                        <label><input type="radio" name="use_sms" value="N" <?php echo $use_sms['N']; ?> /> 사용안함</label>
                        <span class="tbl_sment">
                            zigger에서 NCP SENS와 연동하여 SMS 발송 기능을 사용할 것인지 여부
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>관리자 Feed시 자동 발송</th>
                    <td>
                        <table class="table2">
                            <tbody>
                                <tr>
                                    <th>사용 유무</th>
                                    <td>
                                        <label class="mr10"><input type="radio" name="use_feedsms" value="Y" <?php echo $use_feedsms['Y']; ?> /> 사용</label>
                                        <label><input type="radio" name="use_feedsms" value="N" <?php echo $use_feedsms['N']; ?> /> 사용안함</label>
                                        <span class="tbl_sment">
                                            Manager에서 새로운 관리자 Feed가 발생한 경우 관리자 연락처로 SMS 자동 발송
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>수신 받을 연락처</th>
                                    <td>
                                        <input type="text" name="sms_toadm" class="inp w50p" title="SMS 수신 받을 관리자 연락처" value="<?php echo $write['sms_toadm']; ?>" placeholder="01012345678" />
                                        <span class="tbl_sment">
                                            여러 번호로 발송하는 경우 콤마(,)로 구분<br />ex) 01012345678,01023456789
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>SMS 발송 연락처</th>
                    <td>
                        <input type="text" name="sms_from" class="inp w50p" title="SMS 발신 연락처" value="<?php echo $write['sms_from']; ?>" placeholder="07012345678" />
                        <span class="tbl_sment">
                            발송 연락처가 NCP에 발신자 번호로 등록된 연락처가 아닌 경우 발송 실패됨<br />
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Endpoint</th>
                    <td>
                        <input type="text" name="sms_key1" class="inp w50p" title="SMS Endpoint" value="<?php echo $write['sms_key1']; ?>" placeholder="https://sens.apigw.ntruss.com/sms/v2/services/" />
                    </td>
                </tr>
                <tr>
                    <th>Service ID</th>
                    <td>
                        <input type="text" name="sms_key2" class="inp w50p" title="SMS Service ID" value="<?php echo $write['sms_key2']; ?>" placeholder="ncp:sms:kr:000000000000:project-name" />
                    </td>
                </tr>
                <tr>
                    <th>Access key</th>
                    <td>
                        <input type="text" name="sms_key3" class="inp w50p" title="SMS Access key" value="<?php echo $write['sms_key3']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Secret key</th>
                    <td>
                        <input type="text" name="sms_key4" class="inp w50p" title="SMS Secret key" value="<?php echo $write['sms_key4']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>NCP SENS 신청</th>
                    <td>
                        <a href="https://www.ncloud.com/product/applicationService/sens" target="_blank" class="btn2 small"><i class="fas fa-external-link-alt"></i> NCP SENS 공식 사이트 이동</a>
                        <span class="tbl_sment">
                            zigger와 연동을 위한 NCP SENS Access key 및 Secret key 발급은 공식사이트를 참고<br />
                            Naver Cloud Platform SENS(Simple & Easy Notification Service)
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>
    </form>

</article>
