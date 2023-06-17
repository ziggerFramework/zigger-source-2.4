<div id="sub-tit">
    <h2>포인트 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>회원 포인트 내역 확인 및 관리</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="point"><em>전체 변동 수</em><p><?php echo $act_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=p_in"); ?>"><em>총 누적 포인트</em><p><?php echo $in_total; ?></p></a></li>
        <li><a href="<?php echo $manage->sortlink("?sort=p_out"); ?>"><em>총 차감 포인트</em><p><?php echo $out_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <div id="atcWrap">
        <div class="lef">
            <form <?php echo $this->form(); ?>>
                <table class="table1">
                    <colgroup>
                        <col style="width: 100px;" />
                        <col style="width: auto;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="2" class="tal">포인트 적립/차감</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>회원 id</td>
                            <td>
                                <input type="text" name="id" class="inp w100p" title="회원 id" />
                                <span class="tbl_sment">파이프(|)로 여러 회원 id 입력 가능<br />(ex: member1|member2)</span>
                            </td>
                        </tr>
                        <tr>
                            <td>포인트</td>
                            <td>
                                <input type="text" name="point" class="inp w100p" title="포인트" />
                                <span class="tbl_sment">음수(-)를 입력하는 경우 포인트 차감</span>
                            </td>
                        </tr>
                        <tr>
                            <td>사유</td>
                            <td>
                                <input type="text" name="memo" class="inp w100p" title="사유" />
                                <span class="tbl_sment">회원 포인트 내역에 노출</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="btn-wrap mt20">
                    <div class="center">
                        <button type="submit" class="btn1"><i class="fa fa-check"></i>포인트 반영</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="rig">
            <form id="list-sch" action="" method="get">
                <?php echo $manage->print_hidden_inp(); ?>

                <fieldset>
                    <div class="slt">
                        <select name="where">
                            <option value="mb_id" <?php echo $manage->sch_where("mb_id"); ?>>id</option>
                            <option value="memo" <?php echo $manage->sch_where("memo"); ?>>변동 사유</option>
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
                    <col style="width: 150px;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                    <col style="width: auto;" />
                </colgroup>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">id</a></th>
                        <th><a href="<?php echo $manage->orderlink("p_in"); ?>">적립</a></th>
                        <th><a href="<?php echo $manage->orderlink("p_out"); ?>">차감</a></th>
                        <th><a href="<?php echo $manage->orderlink("memo"); ?>">변동 사유</a></th>
                        <th><a href="<?php echo $manage->orderlink("regdate"); ?>">변동일</a</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($print_arr as $list) { ?>
                        <tr>
                            <td class="no tac"><?php echo $list['no']; ?></td>
                            <td class="tac"><a href="modify?idx=<?php echo $list['mb_idx']; ?>" target="_blank"><strong><?php echo $list['mb_id']; ?></strong></a></td>
                            <td class="tac"><?php echo $list['p_in']; ?></td>
                            <td class="tac"><?php echo $list['p_out']; ?></td>
                            <td><?php echo $list['memo']; ?></td>
                            <td class="tac"><?php echo $list['regdate']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- no data -->
                <?php if (!$print_arr) { ?>
                <p id="list-nodata"><?php echo SET_NODATA_MSG?></p>
                <?php } ?>

                <!-- paging -->
                <div id="list-paging">
                    <?php echo $pagingprint?>
                </div>
            </div>
        </div>

    </article>
