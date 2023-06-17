<div id="sub-tit">
    <h2>가입 회원 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>현재까지 가입한 회원 조회 및 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="result"><em>전체회원</em><p><?php echo $mb_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=emchk"); ?>"><em>이메일 인증 회원</em><p><?php echo $emchk_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=noemchk"); ?>"><em>이메일 미인증 회원</em><p><?php echo $namechk_total; ?></p></a></li>
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
                </select>
            </div>
            <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
            <button type="submit" class="sbm"><i class="fa fa-search"></i>검색</button>
        </fieldset>

    </form>

    <table class="table1 list">
        <colgroup>
            <col style="width: 50px;" />
            <col style="width: 150px;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: 100px;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: auto;" />
            <col style="width: 100px;" />
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th><a href="<?php echo $manage->orderlink("mb_level"); ?>">회원등급</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">id</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_email"); ?>">email</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_phone"); ?>">phone</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_email_chk"); ?>">email 인증</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_name"); ?>">이름</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_point"); ?>">point</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_lately"); ?>">최근로그인</a></th>
                <th><a href="<?php echo $manage->orderlink("mb_regdate"); ?>">회원가입일</a></th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($print_arr as $list) { ?>
            <tr>
                <td class="no tac"><?php echo $list['no']; ?></td>
                <td class="tac"><?php echo $list['mb_level']; ?> (<?php echo $MB['type'][$list['mb_level']]; ?>)</td>
                <td class="tac"><strong><?php echo $list['mb_id']?></strong></td>
                <td class="tac"><a href="../mailler/send?mailto=<?php echo $list['mb_id']; ?>"><?php echo $list['mb_email']; ?></a></td>
                <td class="tac">
                    <?php if ($list['mb_phone']) { ?>
                    <a href="../sms/tomember?smsto=<?php echo $list['mb_id']; ?>"><?php echo $list['mb_phone']; ?></a>
                    <?php } ?>
                </td>
                <td class="tac"><?php echo $list['mb_email_chk']; ?></td>
                <td class="tac"><?php echo $list['mb_name']; ?></td>
                <td class="tac"><?php echo $list['mb_point']; ?></td>
                <td class="tac"><?php echo $list['mb_lately']; ?></td>
                <td class="tac"><?php echo $list['mb_regdate']; ?></td>
                <td class="tac">
                    <a href="./modify<?php echo $manage->lnk_def_param('&idx='.$list['mb_idx']); ?>" class="btn1 small">관리</a>
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
            <a href="./regist" class="btn1"><i class="fa fa-plus"></i>신규 회원 추가</a>
        </div>
    </div>

</article>
