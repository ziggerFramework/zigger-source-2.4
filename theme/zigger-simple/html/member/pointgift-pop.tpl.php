<div class="tit">
	<h2>포인트 선물</h2>
	<a href="#" class="close"><i class="fa fa-times"></i></a>
</div>

<div class="cont">

    <?php if ($is_mbinfo_show) { ?>
    <form action="<?php $this->form(); ?>">
        <input type="hidden" name="reply_parent_idx" value="<?php echo $reply_parent_idx; ?>" />
    	<table class="table_wrt">
            <caption>포인트 선물</caption>
    		<colgroup>
    			<col style="width: 120px;" />
    			<col style="width: auto;" />
    		</colgroup>
    		<tbody>
    			<tr>
    				<th scope="row">보유 포인트</th>
    				<td>
                        <strong><?php echo $now_total_point; ?></strong> Point
                    </td>
    			</tr>
    			<tr>
    				<th scope="row">받는회원</th>
    				<td>
                        <label for="to_mb_id" class="sound_only_ele">아이디 입력 <strong>필수 입력</strong></label>
                        <input type="text" name="to_mb_id" id="to_mb_id" placeholder="아이디 입력" class="inp" title="받는 회원" value="<?php echo $to_mb_id; ?>" required />
                    </td>
    			</tr>
    			<tr>
    				<th scope="row">선물 포인트</th>
    				<td>
                        <label for="to_point" class="sound_only_ele">선물 포인트 <strong>필수 입력</strong></label>
                        <input type="text" name="to_point" id="to_point" placeholder="포인트 입력" class="inp" title="선물 포인트" value="" required />
                        <span class="tbltxt">숫자만 입력해주세요.</span>
                    </td>
    			</tr>
    			<tr>
    				<th scope="row">전달할 내용</th>
    				<td>
                        <label for="article" class="sound_only_ele">전달할 내용 - 5글자 이상 입력해주세요. <strong>필수 입력</strong></label>
                        <textarea name="article" id="article" title="메시지 내용" maxlength="30" style="min-height: 100px;" required></textarea>
                        <span class="tbltxt">5 ~ 30자로 입력해주세요.</span>
                    </td>
    			</tr>
    		</tbody>
    	</table>

        <div class="btn mt10">
            <button type="submit" class="btn1">포인트 선물</button>
        </div>
    </form>
    <?php } ?>

    <?php if (!$is_mbinfo_show) { ?>
    <p class="sment">포인트를 선물할 수 있는 권한이 없습니다.</p>
    <?php } ?>

</div>
