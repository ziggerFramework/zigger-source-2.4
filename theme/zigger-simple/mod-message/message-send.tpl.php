<div class="tit">
	<h2>새로운 메시지 발송</h2>
	<a href="#" class="close"><i class="fa fa-times"></i></a>
</div>

<div class="cont">

    <?php if ($is_mbinfo_show) { ?>
    <form action="<?php $this->form(); ?>">
        <input type="hidden" name="reply_parent_idx" value="<?php echo $reply_parent_idx; ?>" />
    	<table class="table_wrt">
            <caption>새로운 메시지 발송</caption>
    		<colgroup>
    			<col style="width: 120px;" />
    			<col style="width: auto;" />
    		</colgroup>
    		<tbody>
    			<tr>
    				<th scope="row">받는회원</th>
    				<td>
                        <label for="to_mb_id" class="sound_only_ele">아이디 입력 <strong>필수 입력</strong></label>
                        <input type="text" name="to_mb_id" id="to_mb_id" placeholder="아이디 입력" class="inp" title="받는 회원" value="<?php echo $to_mb_id; ?>" required />
                    </td>
    			</tr>
    			<tr>
    				<th scope="row">내용</th>
    				<td>
                        <label for="article" class="sound_only_ele">메시지 내용 - 5글자 이상 입력해주세요. <strong>필수 입력</strong></label>
                        <textarea name="article" id="article" title="메시지 내용" required></textarea>
                        <span class="tbltxt">5글자 이상 입력해주세요.</span>
                    </td>
    			</tr>
    		</tbody>
    	</table>

        <div class="btn mt10">
            <button type="submit" class="btn1">발송</button>
        </div>
    </form>
    <?php } ?>

    <?php if (!$is_mbinfo_show) { ?>
    <p class="sment">메시지를 발송할 수 있는 권한이 없습니다.</p>
    <?php } ?>

</div>
