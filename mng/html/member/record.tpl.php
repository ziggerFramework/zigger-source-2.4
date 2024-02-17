<div id="sub-tit">
    <h2>회원 접속 기록</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 회원 접속 기록 (1시간 이내 재접속은 카운팅 하지 않음)</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><span><em>현재까지 접속 수</em><p><?php echo $visit_total; ?></p></span></li>
        <li><span><em>Device (선택 기간 내)</em><p><?php echo $device_per; ?></p></span></li>
        <li><span><em>회원비율 (선택 기간 내)</em><p><?php echo $member_per; ?></p></span></li>
    </ul>
</div>

<!-- article -->
<article>

    <form id="list-sch" action="" method="get">
        <?php echo $manage->print_hidden_inp(); ?>


        <fieldset>
            <input type="hidden" name="nowdate" class="date" value="<?php echo date("Y-m-d"); ?>" readonly />
            <a href="record">오늘</a>
            <input type="text" name="fdate" class="date" value="<?php echo $fdate; ?>" placeholder="시작일" datepicker readonly />
            <input type="text" name="tdate" class="date" value="<?php echo $tdate; ?>" placeholder="종료일" datepicker readonly />
        </fieldset>

        <fieldset>
            <div class="slt">
                <select name="where">
                    <option value="member.mb_id" <?php echo $manage->sch_where("mb_id"); ?>>id</option>
                    <option value="ip" <?php echo $manage->sch_where("ip"); ?>>접속ip</option>
                    <option value="browser" <?php echo $manage->sch_where("browser"); ?>>OS or browser</option>
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
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("mb_level"); ?>">level</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">id</a></th>
                <th><a href="<?php echo $manage->orderlink("ip"); ?>">접속ip</a></th>
                <th><a href="<?php echo $manage->orderlink("device"); ?>">device</a></th>
                <th>OS</th>
                <th>browser</th>
                <th><a href="<?php echo $manage->orderlink("regdate"); ?>">접속시간</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><?php echo $list['mb_level']; ?> (<?php echo $MB['type'][$list['mb_level']]; ?>)</td>
                <td class="tac"><a href="./modify?idx=<?php echo $list['mb_idx']; ?>" target="_blank"><strong><?php echo $list['mb_id']; ?></strong></a></td>
                <td class="tac"><?php echo $list['ip']; ?></td>
                <td class="tac"><?php echo $list['device']; ?></td>
                <td class="tac"><?php echo $list[0]['user_agent']['os']; ;?></td>
                <td class="tac"><?php echo $list[0]['user_agent']['browser']; ;?></td>
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
