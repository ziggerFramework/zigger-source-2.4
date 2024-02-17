<div id="sub-tit">
    <h2>가입 회원 정보</h2>
    <em><i class="fa fa-exclamation-circle"></i>가입 회원 정보 관리</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="mode" value="mod" />
        <input type="hidden" name="idx" value="<?php echo $write['mb_idx']; ?>" />

        <?php echo $print_target[0]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">회원 기본정보</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>회원 id</th>
                    <td>
                        <strong><?php echo $write['mb_id']; ?></strong>
                        <span class="tbl_sment">아아디는 변경 불가합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>프로필 이미지</th>
                    <td>
                        <?php if ($write[0]['mb_profileimg']) { ?>
                        <div class="mb-profileimg mb10" style="background-image: url('<?php echo $write[0]['mb_profileimg']; ?>');"></div>
                        <?php } else { ?>
                        <span class="tbl_sment mb10">
                            현재 등록된 프로필 이미지가 없습니다. <br />
                            설정 가능한 이미지 최대 용량은 <strong>500Kbyte</strong> 입니다.
                        </span>
                        <?php } ?>
                        <input type="file" name="profileimg" />
                        <?php if ($write[0]['mb_profileimg']) { ?>
                        <span class="tbl_sment">설정 가능한 이미지 최대 용량은 <strong>500Kbyte</strong> 입니다.</span>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>email</th>
                    <td>
                        <input type="text" name="email" class="inp" title="email" value="<?php echo $write['mb_email']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>email 인증 상태</th>
                    <td>
                        <label class="mr10"><input type="radio" name="email_chk" value="Y" <?php echo $email_chk['Y']; ?> /> 인증 완료</label>
                        <label><input type="radio" name="email_chk" value="N" <?php echo $email_chk['N']; ?> /> 인증 미완료</label>
                    </td>
                </tr>
                <tr>
                    <th>password</th>
                    <td>
                        <input type="password" name="pwd" class="inp" title="password" placeholder="변경시에만 입력" />
                    </td>
                </tr>
                <tr>
                    <th>password 확인</th>
                    <td>
                        <input type="password" name="pwd2" class="inp" title="password 확인" placeholder="변경시에만 입력" />
                    </td>
                </tr>
                <tr>
                    <th>이름</th>
                    <td>
                        <input type="text" name="name" class="inp" title="이름" value="<?php echo $write['mb_name']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>회원 등급</th>
                    <td>
                        <select name="level" class="inp">
                            <?php for($i=1;$i<=9;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==$write['mb_level']){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        <span class="tbl_sment">'1등급(<?php echo $MB['type'][1]; ?>)'은 관리페이지 접속 권한이 부여됩니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>성별</th>
                    <td>
                        <label class="mr10"><input type="radio" name="gender" value="M" <?php echo $gender['M']; ?> /> 남자</label>
                        <label><input type="radio" name="gender" value="F" <?php echo $gender['F']; ?> /> 여자</label>
                    </td>
                </tr>
                <tr>
                    <th>휴대전화</th>
                    <td>
                        <input type="text" name="phone" class="inp" title="휴대전화" value="<?php echo $write['mb_phone']; ?>" placeholder="optional" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td>
                        <input type="text" name="telephone" class="inp" title="전화번호" value="<?php echo $write['mb_telephone']; ?>" placeholder="optional" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td>
                        <input type="text" name="address1" class="inp w100" title="주소 - 우편번호" placeholder="우편번호" value="<?php echo $write[0]['mb_address'][0]; ?>" /><br />
                        <input type="text" name="address2" class="inp w33p mt5" title="주소 - 기본주소" placeholder="기본주소" value="<?php echo $write[0]['mb_address'][1]; ?>" />
                        <input type="text" name="address3" class="inp w33p mt5" title="주소 - 상세주소" placeholder="상세주소" value="<?php echo $write[0]['mb_address'][2]; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>point</th>
                    <td>
                        <input type="text" name="point" class="inp w100" title="point" value="<?php echo $write['mb_point']; ?>" />
                        <span class="tbl_sment">포인트가 변동되는 경우 회원에게 변동 내역이 노출됩니다.</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>

        <?php echo $print_target[1]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">회원 접속 정보</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>회원가입일</th>
                    <td>
                        <?php echo $write['mb_regdate']; ?>
                    </td>
                </tr>
                <tr>
                    <th>최근 접속일</th>
                    <td>
                        <?php echo $write['mb_lately']; ?>
                    </td>
                </tr>
                <tr>
                    <th>최근 접속 ip</th>
                    <td>
                        <?php echo $write['mb_lately_ip']; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>

        <?php echo $print_target[2]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="3" class="tal">여분필드 (mb_1 ~ mb_10)</th>
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
                    <th>mb_<?php echo $i; ?></th>
                    <td>
                        <input type="text" name="mb_exp[]" class="inp w100p" value="<?php echo $write['mb_'.$i.'_exp']; ?>" />
                    </td>
                    <td>
                        <input type="text" name="mb_<?php echo $i; ?>" class="inp w100p" value="<?php echo $write['mb_'.$i]; ?>" />
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="#" class="btn2 mr30" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 탈퇴 처리 하시겠습니까? => mode:del"><i class="fa fa-trash-alt"></i>회원 탈퇴</a>
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
