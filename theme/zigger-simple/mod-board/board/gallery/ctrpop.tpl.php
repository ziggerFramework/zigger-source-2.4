<div class="tit">
    <h2>게시글 관리</h2>
    <a href="#" class="close"><i class="fa fa-times"></i></a>
</div>
<form <?php echo $this->form(); ?> class="cont">
    <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
    <input type="hidden" name="category" value="<?php echo $req['category']; ?>" />
    <input type="hidden" name="where" value="<?php echo $req['where']; ?>" />
    <input type="hidden" name="keyword" value="<?php echo $req['keyword']; ?>" />
    <input type="hidden" name="type" value="" />
    <input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
    <input type="hidden" name="cnum" value="<?php echo $cnum_arr; ?>" />

    <p>게시판에서 선택한 <strong><?php echo $slt_count; ?></strong>개의 게시글을</p>
    <div class="ctr-btn">
        <button type="button" class="btn1" id="delete-btn"><i class="fa fa-trash-alt"></i> 삭제</button>
    </div>
    <em>OR</em>
    <p>
        <select name="t_board_id" id="t_board_id" class="inp w100">
            <?php echo $board_opt_list; ?>
        </select>
        게시판으로 아래 작업을 수행
    </p>
    <div class="ctr-btn">
        <button type="button" class="btn2" id="move-btn">이동</button>
        <button type="button" class="btn2" id="copy-btn">복사</button>
    </div>
</form>
