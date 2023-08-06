<div id="sub-tit">
    <h2><?php echo $MB['name']; ?>님의 Mypage</h2>
</div>

<ul class="mypagebox">
    <li>
        <h5>기본 정보</h5>
        <span class="txt">
            <strong class="black"><?php echo $MB['name']; ?></strong> 회원님
        </span>
        <a href="<?php echo PH_DIR; ?>/member/info" class="btn2">회원정보 변경</a>
    </li>
    <li>
        <h5>보유 Point</h5>
        <span class="txt">
            <strong><?php echo $point_total_count; ?></strong> Point
        </span>
        <a href="<?php echo PH_DIR; ?>/member/point" class="btn2">포인트 내역 확인 </a>
    </li>
    <li>
        <h5>받은 Message</h5>
        <span class="txt">
            <strong><?php echo $message_new_count; ?></strong> 개의 새로운 메시지
        </span>
        <a href="<?php echo PH_DIR; ?>/message" class="btn2">모든 메시지 확인</a>
    </li>
    <li>
        <h5>받은 Alarm</h5>
        <span class="txt">
            <strong><?php echo $alarm_new_count; ?></strong> 개의 새로운 알림
        </span>
        <a href="<?php echo PH_DIR; ?>/alarm" class="btn2">모든 알림 확인</a>
    </li>
</ul>

<div class="tblform">

    <h5>기본 정보</h5>
    <table class="table_wrt">
        <caption>회원 기본 정보</caption>
        <colgroup>
            <col style="width: 150px;">
            <col style="width: auto;">
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">아이디</th>
                <td><?php echo $MB['id']; ?></td>
            </tr>
            <tr>
                <th scope="row">이메일</th>
                <td>
                    <?php
                    if ($MB['email']) {
                        echo $MB['email'];
                    } else {
                        echo '등록된 이메일 정보가 없습니다. 이메일 변경을 먼저 해주세요.';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row">이름</th>
                <td><?php echo $MB['name']; ?></td>
            </tr>
            <tr>
                <th scope="row">회원 등급</th>
                <td><?php echo $MB['type'][$MB['level']]; ?></td>
            </tr>
        </tbody>
    </table>
    <div class="btn-wrap">
        <a href="<?php echo PH_DIR; ?>/member/info" class="btn2">기본정보 변경 바로가기</a>
    </div>

    <h5 class="mt30">활동 정보</h5>
    <table class="table_wrt">
        <caption>회원 활동 정보</caption>
        <colgroup>
            <col style="width: 150px;">
            <col style="width: auto;">
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">회원가입일</th>
                <td><?php echo $MB['regdate']; ?></td>
            </tr>
            <tr>
                <th scope="row">최근 로그인</th>
                <td><?php echo $MB['lately']; ?></td>
            </tr>
            <tr>
                <th scope="row">최근 로그인 IP</th>
                <td><?php echo $MB['lately_ip']; ?></td>
            </tr>
        </tbody>
    </table>

</div>
