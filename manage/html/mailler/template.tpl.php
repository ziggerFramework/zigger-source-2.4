<div id="sub-tit">
    <h2>메일 템플릿 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 생성된 메일 템플릿 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="template"><em>전체 템플릿</em><p><?php echo $tpl_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="type" <?php echo $manage->sch_where("type"); ?>>type</option>
                    <option value="title" <?php echo $manage->sch_where("title"); ?>>템플릿 설명</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: 200px;" />
            <col style="width: auto;" />
            <col style="width: 200px;" />
            <col style="width: 100px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("type"); ?>">type</a></th>
                <th><a href="<?php echo $manage->orderlink("title"); ?>">템플릿 설명</a></th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">생성일</a></th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><strong><?php echo $list['type']; ?></strong></td>
                <td><?php echo $list['title']; ?></td>
                <td class="tac"><?php echo $list['regdate']; ?></td>
                <td class="tac">
                    <a href="./modify<?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>" class="btn1 small">관리</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- no data -->
    <?php if (!$print_arr) { ?>
    <p id="list-nodata"><?php echo SET_NODATA_MSG; ?></p>
    <?php } ?>

    <!-- paging -->
    <div id="list-paging">
        <?php echo $pagingprint; ?>
    </div>

    <div class="btn-wrap">
        <div class="center">
            <a href="./regist" class="btn1"><i class="fa fa-plus"></i>메일 템플릿 생성</a>
        </div>
    </div>

</article>
