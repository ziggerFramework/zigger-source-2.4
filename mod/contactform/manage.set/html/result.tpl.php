<div id="sub-tit">
    <h2>온라인 문의</h2>
    <em><i class="fa fa-exclamation-circle"></i>온라인 문의 내역 확인 및 답변</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="result"><em>전체 문의</em><p><?php echo $contactform_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="name" <?php echo $manage->sch_where("name"); ?>>이름</option>
                    <option value="article" <?php echo $manage->sch_where("article"); ?>>문의 내용</option>
                    <option value="email" <?php echo $manage->sch_where("email"); ?>>이메일</option>
                    <option value="phone" <?php echo $manage->sch_where("phone"); ?>>연락처</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: 100px;" />
            <col style="width: 250px;" />
            <col style="width: 200px;" />
            <col style="width: auto;" />
            <col style="width: 250px;" />
            <col style="width: 100px;" />
            <col style="width: 100px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("name"); ?>">이름</a></th>
                <th><a href="<?php echo $manage->orderlink("email"); ?>">이메일</a></th>
                <th><a href="<?php echo $manage->orderlink("phone"); ?>">연락처</a></th>
                <th>내용</th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">문의시간</a></th>
                <th>답변</th>
                <th>보기</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><?php echo $list[0]['print_name']; ?></td>
                <td class="tac"><?php echo $list['email']; ?></td>
                <td class="tac"><?php echo $list['phone']; ?></td>
                <td><a href="./view<?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>"><?php echo $list['article']; ?></a></td>
                <td class="tac"><?php echo $list['regdate']; ?></td>
                <td class="tac"><?php echo $list[0]['print_reply']; ?></td>
                <td class="tac">
                    <a href="./view<?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>" class="btn1 small">보기</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- no data -->
    <?php if (!$print_arr) { ?>
    <p id="list-nodata"><?=SET_NODATA_MSG; ?></p>
    <?php } ?>

    <!-- paging -->
    <div id="list-paging">
    <?php echo $pagingprint; ?>
    </div>

</article>
