<div id="sub-tit">
    <h2>관리자 정보 변경</h2>
    <em><i class="fa fa-exclamation-circle"></i>관리자 정보는 최고 등급의 관리자만 가능</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">관리자 정보 입력</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>회원 id</th>
                    <td>
                        <input type="text" name="id" class="inp" title="id" value="<?php echo $MB['id']; ?>" />
                        <span class="tbl_sment">영어, 숫자 조합으로 입력<br />최소 5자~최대 30자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>프로필 이미지</th>
                    <td>
                        <?php if ($profileimg) { ?>
                        <div class="mb-profileimg mb10" style="background-image: url('<?php echo $profileimg; ?>');"></div>
                        <?php } else { ?>
                        <span class="tbl_sment mb10">현재 등록된 프로필 이미지가 없습니다.</span>
                        <?php } ?>
                        <input type="file" name="profileimg" />
                    </td>
                </tr>
                <tr>
                    <th>이름</th>
                    <td>
                        <input type="text" name="name" class="inp" title="이름" value="<?php echo $MB['name']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>이메일</th>
                    <td>
                        <input type="text" name="email" class="inp" title="이메일" value="<?php echo $MB['email']; ?>" />
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
                    <th>성별</th>
                    <td>
                        <label class="mr10"><input type="radio" name="gender" value="M" <?php echo $gender['M']; ?> /> 남자</label>
                        <label><input type="radio" name="gender" value="F" <?php echo $gender['F']; ?> /> 여자</label>
                    </td>
                </tr>
                <tr>
                    <th>휴대전화</th>
                    <td>
                        <input type="text" name="phone" class="inp" title="휴대전화" value="<?php echo $MB['phone']; ?>" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td>
                        <input type="text" name="telephone" class="inp" title="전화번호" value="<?php echo $MB['telephone']; ?>" />
                        <span class="tbl_sment">하이픈(-) 없이 숫자만 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td>
                        <input type="text" name="address1" class="inp w100" title="주소 - 우편번호" placeholder="우편번호" value="<?php echo $MB[0]['address'][0]; ?>" /><br />
                        <input type="text" name="address2" class="inp w33p mt5" title="주소 - 기본주소" placeholder="기본주소" value="<?php echo $MB[0]['address'][1]; ?>" />
                        <input type="text" name="address3" class="inp w33p mt5" title="주소 - 상세주소" placeholder="상세주소" value="<?php echo $MB[0]['address'][2]; ?>" />
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
