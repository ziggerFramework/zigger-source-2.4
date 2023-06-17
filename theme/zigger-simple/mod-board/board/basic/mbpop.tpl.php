<div class="tit">
	<h2>회원 정보</h2>
	<a href="#" class="close"><i class="fa fa-times"></i></a>
</div>

<div class="cont">

	<!-- 회원정보 표시 -->
	<?php if ($is_mbinfo_show) { ?>
	<table class="mb-tbl">
		<colgroup>
			<col style="width: 120px;" />
			<col style="width: auto;" />
		</colgroup>
		<tbody>
			<tr>
				<th>이름</th>
				<td>
                    <?php if ($mbinfo[0]['mb_profileimg']) { ?>
                    <div class="board-mb-profileimg" style="background-image: url('<?php echo $mbinfo[0]['mb_profileimg']; ?>');"></div>
                    <?php } ?>

                    <?php echo $mbinfo['mb_name']; ?> <?php echo $gender; ?>
                </td>
			</tr>
			<tr>
				<th>아이디</th>
				<td><?php echo $mbinfo['mb_id']; ?></td>
			</tr>
			<tr>
				<th>회원등급</th>
				<td><?php echo $MB['type'][$mbinfo['mb_level']]; ?></td>
			</tr>
			<tr>
				<th>회원가입일</th>
				<td><?php echo $mbinfo['mb_regdate']; ?></td>
			</tr>
			<tr>
				<th>최근로그인</th>
				<td><?php echo $mbinfo['mb_lately']; ?></td>
			</tr>
		</tbody>
	</table>
	<?php } ?>

	<!-- 정보를 볼 권한이 없는 경우 -->
	<?php if (!$is_mbinfo_show) { ?>
	<p class="sment">회원 정보를 볼 수 있는 권한이 없거나,<br />회원이 탈퇴하여 정보를 불러올 수 없습니다.</p>
	<?php } ?>

    <div class="mb-btn mt10">
    	<a href="<?php echo $get_link; ?>" class="btn2" id="move-btn"><i class="fa fa-search"></i> 작성글 보기</a>
        <?php if ($is_mbinfo_show) { ?>
    	<a href="#" class="btn2" data-message-send="<?php echo $mbinfo['mb_id']; ?>" data-message-send-reply="">메시지 발송</a>
        <?php } ?>
    </div>

</div>
