<div id="sub-tit">
    <h2>현재 접속 세션</h2>
    <em><i class="fa fa-exclamation-circle"></i>최근 10분 동안 활동 내역이 있는 세션 리스트</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="session"><em>현재 접속 세션 수</em><p><?php echo $stat_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="mb_id" <?php echo $manage->sch_where("mb_id"); ?>>id</option>
                    <option value="mb_email" <?php echo $manage->sch_where("mb_email"); ?>>email</option>
                    <option value="mb_name" <?php echo $manage->sch_where("mb_name"); ?>>이름</option>
                    <option value="ip" <?php echo $manage->sch_where("ip"); ?>>ip</option>
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>
    </form>

    <table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: 150px;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("mb_level"); ?>">회원등급</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">id</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_email"); ?>">email</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_name"); ?>">이름</a></th>
                <th><a href="<?php echo $manage->orderlink("ip"); ?>">ip</a</th>
                    <th><a href="<?php echo $manage->orderlink("regdate"); ?>">마지막 활동</a</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($print_arr as $list) { ?>
                    <tr>
                        <td class="no tac"><?php echo $list['no']; ?></td>
                        <td class="tac"><?php echo $list['mb_level']; ?> (<?php echo $MB['type'][$list['mb_level']]; ?>)</td>
                        <td class="tac"><a href="modify?idx=<?php echo $list['mb_idx']; ?>" target="_blank"><strong><?php echo $list['mb_id']; ?></strong></a></td>
                        <td class="tac"><a href="../mailler/send?mailto=<?php echo $list['mb_email']; ?>"><?php echo $list['mb_email']; ?></a></td>
                        <td class="tac"><?php echo $list['mb_name']; ?></td>
                        <td class="tac"><?php echo $list['ip']; ?></td>
                        <td class="tac"><?php echo $list['regdate']; ?></td>
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

        </article>
