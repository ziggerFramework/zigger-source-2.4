<div id="sub-tit">
    <h2>기본정보 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>사이트 기본 정보 관리</em>
</div>

<!-- article -->
<article>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <?php echo $print_target[0]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">사이트 기본 정보</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>사이트명</th>
                    <td>
                        <input type="text" name="title" class="inp" title="사이트명" value="<?php echo $write['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>도메인</th>
                    <td>
                        <input type="text" name="domain" class="inp" title="도메인" value="<?php echo $write['domain']; ?>" placeholder="http://mydomain" />
                        <span class="tbl_sment">외부 공유 링크, 웹메일 본문 내 접속 링크 등에 활용되므로 정확한 입력 필요</span>
                    </td>
                </tr>
                <tr>
                    <th>사이트 설명</th>
                    <td>
                        <input type="text" name="description" class="inp w100p" title="사이트 설명" value="<?php echo $write['description']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>관리자 이메일</th>
                    <td>
                        <input type="text" name="email" class="inp" title="관리자 이메일" value="<?php echo $write['email']; ?>" placeholder="email@mydomain" />
                        <span class="tbl_sment">시스템의 웹메일 발송 주소로 활용 되므로 정확한 입력 필요</span>
                    </td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td>
                        <input type="text" name="tel" class="inp" title="전화번호" value="<?php echo $write['tel']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>반응형 모바일</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_mobile" value="Y" <?php echo $use_mobile['Y']; ?> /> 사용</label>
                        <label><input type="radio" name="use_mobile" value="N" <?php echo $use_mobile['N']; ?> /> 사용안함</label>
                        <span class="tbl_sment">반응형 모바일이 비활성화 된 경우 모바일 Device에서 PC레이아웃이 출력됨</span>
                    </td>
                </tr>
                <tr>
                    <th>로고</th>
                    <td>
                        <input type="file" name="logo" />

                        <?php if ($is_logo_show) { ?>
                        <dl class="fileview">
                            <dt><img src="<?php echo $write[0]['logo']['replink']; ?>" /></dt>
                            <dd>
                                <strong>등록된 파일</strong>
                                <a href="<?php echo $write[0]['logo']['replink']; ?>" target="_blank"><?php echo $write[0]['logo']['orgfile']; ?></a>
                                <label class="ml10"><input type="checkbox" name="logo_del" value="checked" /> 삭제</label>
                            </dd>
                        </dl>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>파비콘</th>
                    <td>
                        <input type="file" name="favicon" />
                        <span class="tbl_sment">.ico 파일만 첨부 가능</span>

                        <?php if ($is_favicon_show) { ?>
                        <dl class="fileview no-dt">
                            <dd>
                                <strong>등록된 파일</strong>
                                <a href="<?php echo $write[0]['favicon']['replink']; ?>" target="_blank"><?php echo $write[0]['favicon']['orgfile']; ?></a>
                                <label class="ml10"><input type="checkbox" name="favicon_del" value="checked" /> 삭제</label>
                            </dd>
                        </dl>
                        <?php } ?>
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
                    <th colspan="2" class="tal">회원가입 입력 항목 설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>회원 이메일 인증</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_emailchk" value="Y" <?php echo $use_emailchk['Y']; ?> /> 사용</label>
                        <label><input type="radio" name="use_emailchk" value="N" <?php echo $use_emailchk['N']; ?> /> 사용안함</label>
                    </td>
                </tr>
                <tr>
                    <th>휴대전화 입력</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_mb_phone" value="Y" <?php echo $use_mb_phone['Y']; ?> /> 필수입력</label>
                        <label class="mr10"><input type="radio" name="use_mb_phone" value="O" <?php echo $use_mb_phone['O']; ?> />  선택입력</label>
                        <label><input type="radio" name="use_mb_phone" value="N" <?php echo $use_mb_phone['N']; ?> /> 사용안함</label>
                    </td>
                </tr>
                <tr>
                    <th>휴대전화 인증</th>
                    <td>
                        <?php if ($write['use_sms'] != 'Y') { ?>
                        <p class="article-wait">
                            <i class="fa fa-info-circle"></i>
                            SMS 문자(NCP SENS) 발송 기능이 비활성화 되어 있습니다.
                            <em>'<a href="<?php echo PH_MANAGE_DIR; ?>/siteinfo/plugins">기본 관리도구 &gt; 플러그인 및 기능 설정</a>' 에서 설정 후 이용해 주시길 바랍니다.</em>
                        </p>
                        <?php } ?>
                        <label class="mr10"><input type="radio" name="use_phonechk" value="Y" <?php echo $use_phonechk['Y']; ?> /> SMS 번호인증 사용</label>
                        <label class="mr10"><input type="radio" name="use_phonechk" value="N" <?php echo $use_phonechk['N']; ?> /> SMS 번호인증 사용안함</label>
                        <span class="tbl_sment">휴대전화 인증이 활성화한 경우 회원가입 및 정보수정시 SMS 문자를 통한 본인 인증 수행</span>
                    </td>
                </tr>
                <tr>
                    <th>전화번호 입력</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_mb_telephone" value="Y" <?php echo $use_mb_telephone['Y']; ?> /> 필수입력</label>
                        <label class="mr10"><input type="radio" name="use_mb_telephone" value="O" <?php echo $use_mb_telephone['O']; ?> />  선택입력</label>
                        <label><input type="radio" name="use_mb_telephone" value="N" <?php echo $use_mb_telephone['N']; ?> /> 사용안함</label>
                    </td>
                </tr>
                <tr>
                    <th>주소 입력</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_mb_address" value="Y" <?php echo $use_mb_address['Y']; ?> /> 필수입력</label>
                        <label class="mr10"><input type="radio" name="use_mb_address" value="O" <?php echo $use_mb_address['O']; ?> />  선택입력</label>
                        <label><input type="radio" name="use_mb_address" value="N" <?php echo $use_mb_address['N']; ?> /> 사용안함</label>
                    </td>
                </tr>
                <tr>
                    <th>성별 선택</th>
                    <td>
                        <label class="mr10"><input type="radio" name="use_mb_gender" value="Y" <?php echo $use_mb_gender['Y']; ?> /> 사용</label>
                        <label class="mr10"><input type="radio" name="use_mb_gender" value="N" <?php echo $use_mb_gender['N']; ?> /> 사용안함</label>
                    </td>
                </tr>
                <tr>
                    <th>회원 등급별 명칭</th>
                    <td>
                        <table class="table2 with-thead">
                            <colgroup>
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                                <col style="width: 10%;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>등급</th>
                                    <th>명칭</th>
                                    <th>등급</th>
                                    <th>명칭</th>
                                    <th>등급</th>
                                    <th>명칭</th>
                                    <th>등급</th>
                                    <th>명칭</th>
                                    <th>등급</th>
                                    <th>명칭</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="tac">1 (최고관리자)</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(1)" value="<?php echo $mb_division[1]; ?>" class="inp w100p" /></td>
                                    <th class="tac">2</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(2)" value="<?php echo $mb_division[2]; ?>" class="inp w100p" /></td>
                                    <th class="tac">3</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(3)" value="<?php echo $mb_division[3]; ?>" class="inp w100p" /></td>
                                    <th class="tac">4</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(4)" value="<?php echo $mb_division[4]; ?>" class="inp w100p" /></td>
                                    <th class="tac">5</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(5)" value="<?php echo $mb_division[5]; ?>" class="inp w100p" /></td>
                                </tr>
                                <tr>
                                    <th class="tac">6</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(6)" value="<?php echo $mb_division[6]; ?>" class="inp w100p" /></td>
                                    <th class="tac">7</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(7)" value="<?php echo $mb_division[7]; ?>" class="inp w100p" /></td>
                                    <th class="tac">8</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(8)" value="<?php echo $mb_division[8]; ?>" class="inp w100p" /></td>
                                    <th class="tac">9 (일반회원)</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(9)" value="<?php echo $mb_division[9]; ?>" class="inp w100p" /></td>
                                    <th class="tac">10 (비회원)</th>
                                    <td class="tac"><input type="text" name="mb_division[]" title="회원 등급별 명칭(10)" value="<?php echo $mb_division[10]; ?>" class="inp w100p" /></td>
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
                    <th colspan="2" class="tal">정책 및 약관</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>개인정보처리방침</th>
                    <td>
                        <textarea id="privacy" name="privacy"><?php echo $write['privacy']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('privacy');</script>
                    </td>
                </tr>
                <tr>
                    <th>이용약관</th>
                    <td>
                        <textarea id="policy" name="policy"><?php echo $write['policy']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('policy');</script>
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
                    <th colspan="3" class="tal">여분필드 (st_1 ~ st_10)</th>
                </tr>
                <tr>
                    <th class="tal">필드명</th>
                    <th class="tal">필드 설명</th>
                    <th class="tal">저장 값</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 10; $i++) { ?>
                <tr>
                    <th>st_<?php echo $i; ?></th>
                    <td>
                        <input type="text" name="st_exp[]" class="inp w100p" value="<?php echo $write['st_'.$i.'_exp']; ?>" />
                    </td>
                    <td>
                        <input type="text" name="st_<?php echo $i; ?>" class="inp w100p" value="<?php echo $write['st_'.$i]; ?>" />
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>
    </form>

</article>
