<div id="sub-tit">
    <h2>생성된 배너</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 생성된 배너 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="result"><em>전체 배너</em><p><?php echo $bn_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="bn_key" <?php echo $manage->sch_where("bn_key"); ?>>key</option>
                    <option value="title" <?php echo $manage->sch_where("title"); ?>>제목</option>
                    <option value="link" <?php echo $manage->sch_where("link"); ?>>link</option>
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
            <col style="width: 100px;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: 100px;" />
            <col style="width: auto;" />
            <col style="width: 100px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("bn_key"); ?>">key</a></th>
                <th><a href="<?php echo $manage->orderlink("zindex"); ?>">배너 순서</a></th>
                <th>image</th>
                <th><a href="<?php echo $manage->orderlink("title"); ?>">제목</a></th>
                <th><a href="<?php echo $manage->orderlink("link"); ?>">link</a></th>
                <th><a href="<?php echo $manage->orderlink("link_target"); ?>">link target</a></th>
                <th><a href="<?php echo $manage->orderlink("hit"); ?>">클릭 수</a></th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">생성일</a></th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><strong><?php echo $list['bn_key']; ?></strong></td>
                <td class="tac"><?php echo $list['zindex']; ?></td>
                <td class="tac">
                    <?php if ($list[0]['thumbnail']) { ?>
                        <img src="<?php echo $list[0]['thumbnail']; ?>" alt="썸네일" class="tmb" />
                    <?php } ?>
                </td>
                <td class="tac"><?php echo $list['title']; ?></td>
                <td class="tac"><a href="<?php echo $list['link']; ?>" target="_blank"><?php echo $list['link']; ?></a></td>
                <td class="tac"><?php echo $list['link_target']; ?></td>
                <td class="tac"><?php echo $list['hit']; ?></td>
                <td class="tac"><?php echo $list['regdate']; ?></td>
                <td class="tac">
                    <a href="./modify?idx=<?php echo $list['idx']; ?><?php echo $manage->lnk_def_param('&idx='.$list['idx']); ?>" class="btn1 small">관리</a>
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
            <a href="./regist" class="btn1"><i class="fa fa-plus"></i>신규 배너 생성</a>
        </div>
    </div>

</article>
