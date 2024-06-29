<div id="sub-tit">
    <h2>Recent Posts</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규게시글 레코딩 설정 및 관리</em>
</div>

<!-- article -->
<article>

<table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: 200px;" />
            <col style="width: 120px;" />
            <col style="width: auto;" />
            <col style="width: 200px;" />
            <col style="width: 150px;" />
            <col style="width: 100px;" />
            <col style="width: 200px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("board_id"); ?>">게시판 id</a></th>
                <th><a href="<?php echo $manage->orderlink("bo_idx"); ?>">게시글 번호</a></th>
                <th><a href="<?php echo $manage->orderlink("subject"); ?>">제목</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">회원 ID</a></th>
                <th><a href="<?php echo $manage->orderlink("writer"); ?>">작성자</a></th>
                <th><a href="<?php echo $manage->orderlink("view"); ?>">조회수</a></th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">기록일</a></th>
            </tr>
        </thead>
        <tbody id="boardList">
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><a href="<?php echo $list[0]['board-link']; ?>" target="_blank"><strong><?php echo $list['board_id']; ?></strong></a></td>
                <td class="tac"><?php echo $list['bo_idx']; ?></td>
                <td><a href="<?php echo $list[0]['view-link']; ?>" target="_blank"><strong><?php echo $list['subject']; ?></strong></a></td>
                <td class="tac">
                    <?php if ($list['mb_id']) { ?>
                    <a href="<?php echo $list[0]['member-link']; ?>" target="_blank"><strong><?php echo $list['mb_id']; ?></strong></a>
                    <?php } else { echo '-'; } ?>
                </td>
                <td class="tac"><?php echo $list['writer']; ?></td>
                <td class="tac"><?php echo $list['view']; ?></td>
                <td class="tac"><?php echo $list[0]['regdate']; ?></td>
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
