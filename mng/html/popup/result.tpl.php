<div id="sub-tit">
    <h2>생성된 팝업</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 생성된 팝업 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="result"><em>전체 팝업</em><p><?php echo $pop_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=usepop"); ?>"><em>사용 팝업</em><p><?php echo $use_pop; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=nousepop"); ?>"><em>미사용 팝업</em><p><?php echo $notuse_pop; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="id" <?php echo $manage->sch_where("id"); ?>>id</option>
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
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: 100px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("id"); ?>">id</a></th>
                <th>image</th>
                <th><a href="<?php echo $manage->orderlink("title"); ?>">제목</a></th>
                <th><a href="<?php echo $manage->orderlink("link"); ?>">link</a></th>
                <th>노출 대상</th>
                <th><a href="<?php echo $manage->orderlink("show_from"); ?>">노출 기간</a></th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">생성일</a></th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><strong><?php echo $list['id']; ?></strong></td>
                <td class="tac">
                    <?php if($list[0]['thumbnail']){ ?>
                        <img src="<?php echo $list[0]['thumbnail']; ?>" alt="썸네일" class="tmb" />
                    <?php } ?>
                </td>
                <td class="tac"><?php echo $list['title']; ?></td>
                <td class="tac"><a href="<?php echo $list['link']; ?>" target="_blank"><?php echo $list['link']; ?></a></td>
                <td class="tac"><?php echo $list['level']?></td>
                <td class="tac"><?php echo $list['show']?></td>
                <td class="tac"><?php echo $list['regdate']?></td>
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
            <a href="./regist" class="btn1"><i class="fa fa-plus"></i>신규 팝업 생성</a>
        </div>
    </div>

</article>
