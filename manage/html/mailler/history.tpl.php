<div id="sub-tit">
    <h2>메일 발송 내역</h2>
    <em><i class="fa fa-exclamation-circle"></i>메일 발송 내역 확인</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="history"><em>전체 발송 수</em><p><?php echo $sent_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=to_mb"); ?>"><em>특정회원 발송</em><p><?php echo $to_mb_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=level_from"); ?>"><em>범위지정 발송</em><p><?php echo $level_from_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="to_mb" <?php echo $manage->sch_where("type"); ?>>수신 회원 ID</option>
                    <option value="subject" <?php echo $manage->sch_where("subject"); ?>>제목</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <form <?php $this->form(); ?> class="w-full">
        <input type="hidden" name="mode" value="" />
        <table class="table1 list">
            <colgroup>
                <col style="width: 63px;" />
                <col style="width: 50px;" />
                <col style="width: 300px;" />
                <col style="width: 200px;" />
                <col style="width: auto;" />
                <col style="width: 200px;" />
                <col style="width: 100px;" />
            </colgroup>
            <thead>
                <tr>
                    <th><label><input type="checkbox" class="cnum_allchk" /></label></th>
                    <th>No.</th>
                    <th>수신 범위</th>
                    <th><a href="<?php echo $manage->orderlink("to_mb"); ?>">수신 회원 ID</a></th>
                    <th><a href="<?php echo $manage->orderlink("subject"); ?>">제목</a></th>
                    <th><a href="<?php echo $manage->orderlink("regdate"); ?>">발송일</a></th>
                    <th>보기</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($print_arr as $list) { ?>
                <tr>
                    <td class="chk"><label><input type="checkbox" name="cnum[]" value="<?php echo $list['idx']; ?>" /></label></td>
                    <td class="no tac"><?php echo $list['no']; ?></td>
                    <td class="tac"><?php echo $list[0]['print_level']; ?></td>
                    <td class="tac"><?php echo $list[0]['print_to_mb']; ?></td>
                    <td><a href="./historyview<?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>"><?php echo $list['subject']; ?></a></td>
                    <td class="tac"><?php echo $list['regdate']; ?></td>
                    <td class="tac">
                        <a href="./historyview<?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>" class="btn1 small">보기</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            
        </table>
        
        <?php if ($print_arr) { ?>
        <div class="mt20">
            <button type="button" class="btn2 small list-del-btn" data-form-before-confirm="정말로 삭제 하시겠습니까? => mode:del">선택 삭제</button>
        </div>
        <?php } ?>

        <!-- no data -->
        <?php if (!$print_arr) { ?>
        <p id="list-nodata"><?php echo SET_NODATA_MSG; ?></p>
        <?php } ?>

        <!-- paging -->
        <div id="list-paging">
            <?php echo $pagingprint; ?>
        </div>
    </form>

</article>
