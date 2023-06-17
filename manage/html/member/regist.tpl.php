<div id="sub-tit">
    <h2>신규 회원 추가</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규 회원 직접 추가</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">회원 기본정보 입력</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>회원 id</th>
                    <td>
                        <input type="text" name="id" class="inp" title="회원 id" />
                        <span class="tbl_sment">영어, 숫자 조합으로 입력<br />최소 5자~최대 30자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>email</th>
                    <td>
                        <input type="text" name="email" class="inp" title="email" />
                        <span class="tbl_sment">입력한 이메일로 이메일 인증 메일이 발송 됩니다.<br />단, 이메일 인증 기능 비활성 상태인 경우 인증 없이 가입 완료됩니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>password</th>
                    <td>
                        <input type="password" name="pwd" class="inp" title="password" />
                        <span class="tbl_sment">최소 5자~최대 30자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>password 확인</th>
                    <td>
                        <input type="password" name="pwd2" class="inp" title="password 확인" />
                        <span class="tbl_sment">비밀번호 확인을 위해 재입력</span>
                    </td>
                </tr>
                <tr>
                    <th>이름</th>
                    <td>
                        <input type="text" name="name" class="inp" title="이름" />
                    </td>
                </tr>
                <tr>
                    <th>회원 등급</th>
                    <td>
                        <select name="level" class="inp">
                            <?php for ($i = 1; $i <= 9; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($i==9){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        <span class="tbl_sment">'1등급(<?php echo $MB['type'][1]; ?>)'은 관리페이지 접속 권한이 부여됩니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>성별</th>
                    <td>
                        <label class="mr10"><input type="radio" name="gender" value="M" checked /> 남자</label>
                        <label><input type="radio" name="gender" value="F" /> 여자</label>
                    </td>
                </tr>
                <tr>
                    <th>휴대전화</th>
                    <td>
                        <input type="text" name="phone" class="inp" title="휴대전화" placeholder="optional" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td>
                        <input type="text" name="telephone" class="inp" title="전화번호" placeholder="optional" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td>
                        <input type="text" name="address1" class="inp w100" title="주소 - 우편번호" placeholder="우편번호" /><br />
                        <input type="text" name="address2" class="inp w33p mt5" title="주소 - 기본주소" placeholder="기본주소" />
                        <input type="text" name="address3" class="inp w33p mt5" title="주소 - 상세주소" placeholder="상세주소" />
                    </td>
                </tr>
                <tr>
                    <th>point</th>
                    <td>
                        <input type="text" name="point" class="inp w100" title="point" value="0" />
                        <span class="tbl_sment">포인트가 입력되는 경우 회원에게 적립 내역이 노출됩니다.</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>

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
                <?php for ($i=1; $i <= 10; $i++) { ?>
                <tr>
                    <th>mb_<?php echo $i; ?></th>
                    <td>
                        <input type="text" name="mb_exp[]" class="inp w100p" />
                    </td>
                    <td>
                        <input type="text" name="mb_<?php echo $i; ?>" class="inp w100p"  />
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
