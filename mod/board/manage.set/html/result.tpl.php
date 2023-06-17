<div id="sub-tit">
    <h2>생성된 게시판</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 생성된 게시판 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="result"><em>전체 게시판</em><p><?php echo $board_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="config.cfg_value" <?php echo $manage->sch_where("config.cfg_value"); ?>>id</option>
                    <option value="board_name_tbl.cfg_value" <?php echo $manage->sch_where("board_name_tbl.cfg_value"); ?>>게시판 타이틀</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: 200px;" />
            <col style="width: 220px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("config.cfg_value"); ?>">id</a></th>
                <th><a href="<?php echo $manage->orderlink("config.cfg_type"); ?>">게시판 타이틀</a></th>
                <th>게시글 수</th>
                <th>접근 권한</th>
                <th>읽기 권한</th>
                <th>작성 권한</th>
                <th><a href="<?php echo $manage->orderlink("config.cfg_regdate"); ?>">생성일</a></th>
                <th>복제 생성</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody id="boardList">
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><strong><?php echo $list['id']; ?></strong></td>
                <td class="tac"><?php echo $list['title']; ?></td>
                <td class="tac"><a href="./board?id=<?php echo $list['id']; ?>"><strong><?php echo $list[0]['data_total']; ?></strong>건</a></td>
                <td class="tac"><?php echo $list['list_level']; ?></td>
                <td class="tac"><?php echo $list['read_level']; ?></td>
                <td class="tac"><?php echo $list['write_level']; ?></td>
                <td class="tac"><?php echo $list['regdate']; ?></td>
                <td class="tac">
                    <form <?php $this->form($list['id']); ; ?>>
                        <input type="text" name="clone_id" class="inp w100" placeholder="생성할 id" title="생성할 id" />
                        <input type="hidden" name="board_id" value="<?php echo $list['id']; ?>" />
                        <button type="submit" class="btn2 small clone-btn" data-form-before-confirm="게시판 복제를 수행 하시겠습니까?">복제</button>
                    </form>
                </td>
                <td class="tac">
                    <a href="./board?id=<?php echo $list['id']; ?>" class="btn2 small">게시글 관리</a>
                    <a href="./modify<?php echo $manage->lnk_def_param('&id='.$list['id']); ?>" class="btn1 small">설정 관리</a>
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

    <div class="btn-wrap">
        <div class="center">
            <a href="./regist" class="btn1"><i class="fa fa-plus"></i>신규 게시판 생성</a>
        </div>
    </div>

</article>
