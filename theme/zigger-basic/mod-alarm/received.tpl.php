<div id="sub-tit">
    <h2><?php echo $MB['name']; ?>님의 알림함</h2>
</div>

<div class="mypoint">
	알림함에 <strong><?php echo $total_new_alarm; ?></strong> 개의 새로운 알림이 도착했습니다.
</div>

<div class="btn-wrap mb20">
    <div class="right">
        <a href="?mode=read&allcheck=1&page=<?php echo $page; ?>" class="btn2">전체 읽음으로 변경</a>
    </div>
</div>

<table class="table listtbl">
    <caption>나의 알림함</caption>
	<colgroup>
		<col style="width: 70px;" />
		<col style="width: 250px;" />
        <col style="width: auto;" />
		<col style="width: 250px;" />
	</colgroup>
	<thead>
		<tr>
			<th scope="col">No.</th>
			<th scope="col">출처</th>
			<th scope="col" class="tal">내용</th>
			<th scope="col">수신시간</th>
		</tr>
	</thead>
	<tbody>

		<?php foreach ($print_arr as $list) { ?>
		<tr>
			<td class="no"><?php echo $list['no']; ?></td>
			<td>
                <?php echo $list['msg_from']; ?>
            </td>
            <td class="tal">
                <a href="<?php echo $list[0]['view-link']; ?>">
                    <?php
                    if ($list['chked'] != 'Y') {
                        echo '<strong>'.$list['memo'].'</strong>';
                    } else {
                        echo $list['memo'];
                    }
                    ?>
                </a>
            </td>
			<td><?php echo $list['regdate']; ?></td>
		</tr>
		<?php } ?>

		<?php if (!$print_arr) { ?>
		<tr>
			<td colspan="4"><?php echo SET_NODATA_MSG; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>

<?php echo $pagingprint; ?>
