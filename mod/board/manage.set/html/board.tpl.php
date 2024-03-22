<div id="sub-tit">
    <h2>게시판 게시글 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>게시판 게시글 관리 및 새로운 글 작성</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="board?id=<?php echo $board_id; ?>"><em>전체 게시글</em><p><?php echo $data_total; ?></p></a></li>
        <?php echo $category_sort; ?>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="category" value="<?php echo $category; ?>" />
        <input type="hidden" name="id" value="<?php echo $board_id; ?>" />

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="all" <?php echo $manage->sch_where("all"); ?>>전체</option>
                    <option value="subjectAndArticle" <?php echo $manage->sch_where("subjectAndArticle"); ?>>제목+내용</option>
                    <option value="subject" <?php echo $manage->sch_where("subject"); ?>>제목</option>
                    <option value="article" <?php echo $manage->sch_where("article"); ?>>내용</option>
                    <option value="writer" <?php echo $manage->sch_where("writer"); ?>>작성자</option>
                    <option value="mb_id" <?php echo $manage->sch_where("mb_id"); ?>>회원 아이디</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
        <input type="hidden" name="category" value="<?php echo $category; ?>" />
        <input type="hidden" name="where" value="<?php echo $where; ?>" />
        <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" />
        <input type="hidden" name="request" value="manage" />

        <table class="table1 list">
            <colgroup>
                <col style="width: 50px;" />
                <col style="width: 80px;" />
                <col style="width: auto;" />
                <col style="width: 200px;" />
                <col style="width: 200px;" />
                <col style="width: 100px;" />
                <col style="width: 100px;" />
            </colgroup>
            <thead>
                <tr>
                    <th><label><input type="checkbox" class="cnum_allchk" /></label></th>
                    <th>No.</th>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>날짜</th>
                    <th>조회</th>
                    <th>좋아요</th>
                </tr>
            </thead>

            <tbody id="boardNoticeDataList">
                <?php foreach ($print_notice as $list) { ?>
                    <tr>
                        <td class="chk"><label><input type="checkbox" name="cnum[]" value="<?php echo $list['idx']; ?>" /></label></td>
                        <td class="no tac"><strong>공지</strong></td>
                        <td>
                            <a href="<?php echo $list[0]['get_link']; ?>" class="sbj">
                                <?php echo $list[0]['secret_ico']; ?>
                                <?php echo $list[0]['subject']; ?>
                                <?php echo $list[0]['file_ico']; ?>
                                <?php echo $list[0]['new_ico']; ?>
                                <?php echo $list[0]['hot_ico']; ?>

                                <?php if ($is_comment_show) { ?>
                                    <span class="cmt"><?php echo $list[0]['comment_cnt']; ?></span>
                                <?php } ?>
                            </a>
                        </td>
                        <td class="tac"><?php echo $list[0]['writer']; ?></td>
                        <td class="tac"><?php echo $list['regdate']; ?></td>
                        <td class="tac"><?php echo $list['view']; ?></td>
                        <td class="tac"><strong><?php echo $list['likes_cnt']; ?></strong>/<?php echo $list['unlikes_cnt']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>

            <tbody id="boardDataList">
                <?php foreach ($print_arr as $list) { ?>
                <tr>
                    <td class="chk"><label><input type="checkbox" name="cnum[]" value="<?php echo $list['idx']; ?>" /></label></td>
                    <td class="no tac"><?php echo $list[0]['number']; ?></td>
                    <td>
                        <a href="<?php echo $list[0]['get_link']; ?>" class="sbj">

                            <?php echo $list[0]['secret_ico']; ?>

                            <?php if ($is_category_show) { ?>
                            <strong class="cat">[<?php echo $list['category']; ?>]</strong>
                            <?php } ?>

                            <?php echo $list[0]['subject']; ?>

                            <?php echo $list[0]['file_ico']; ?>
                            <?php echo $list[0]['new_ico']; ?>
                            <?php echo $list[0]['hot_ico']; ?>

                            <?php if ($is_comment_show) { ?>
                            <span class="cmt"><?php echo $list[0]['comment_cnt']; ?></span>
                            <?php } ?>
                        </a>
                    </td>
                    <td class="tac"><?php echo $list[0]['writer']; ?></td>
                    <td class="tac"><?php echo $list['regdate']; ?></td>
                    <td class="tac"><?php echo $list['view']; ?></td>
                    <td class="tac"><strong><?php echo $list['likes_cnt']; ?></strong>/<?php echo $list['unlikes_cnt']; ?></td>
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

        <div class="btn-wrap">
            <div class="center">
                <button id="list-ctr-btn" class="btn2">선택 관리</button>
                <?php echo $write_btn; ?>
            </div>
        </div>
    </form>

</article>
