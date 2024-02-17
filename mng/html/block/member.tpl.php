<div id="sub-tit">
    <h2>회원 접속 차단</h2>
    <em><i class="fa fa-exclamation-circle"></i>특정 회원 사이트 접속 차단 설정</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="member"><em>전체 차단 수</em><p><?php echo $block_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <div id="atcWrap">
        <div class="lef">
            <form <?php echo $this->form(); ?>>
                <input type="hidden" name="mode" value="add" />

                <table class="table1">
                    <colgroup>
                        <col style="width: 100px;" />
                        <col style="width: auto;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="2" class="tal">회원 차단 추가</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>회원 id</td>
                            <td>
                                <input type="text" name="id" class="inp w100p" title="차단할 회원 id" />
                            </td>
                        </tr>
                        <tr>
                            <td>사유</td>
                            <td>
                                <input type="text" name="memo" class="inp w100p" title="차단 사유" />
                                <span class="tbl_sment">차단된 회원에게 사유가 노출됩니다.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="btn-wrap mt20">
                    <div class="center">
                        <button type="submit" class="btn1"><i class="fa fa-check"></i>추가</button>
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
                            <option value="mb_id" <?php echo $manage->sch_where("mb_id"); ?>>회원 id</option>
                            <option value="memo" <?php echo $manage->sch_where("memo"); ?>>사유</option>
                        </select>
                    </div>
                    <input type="text" name="keyword" class="keyword" value="<?php echo $keyword; ?>" placeholder="검색어를 입력하세요." />
                    <button type="submit" class="btn1 small sbm"><i class="fa fa-search"></i>검색</button>
                </fieldset>
            </form>

            <form <?php echo $this->form2(); ?>>
                <input type="hidden" name="mode" value="del" />
                <input type="hidden" name="idx" value="" />

                <table class="table1 list">
                    <colgroup>
                        <col style="width: 50px;" />
                        <col style="width: 300px;" />
                        <col style="width: auto;" />
                        <col style="width: 250px;" />
                        <col style="width: 100px;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th><a href="<?php echo $manage->orderlink("mb_id"); ?>">회원 id</a></th>
                            <th><a href="<?php echo $manage->orderlink("memo"); ?>">사유</a></th>
                            <th><a href="<?php echo $manage->orderlink("regdate"); ?>">적용일</a></th>
                            <th>삭제</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($print_arr as $list) {
                        ?>
                        <tr>
                            <td class="no tac"><?php echo $list['no']; ?></td>
                            <td class="tac"><strong><?php echo $list['mb_id']; ?> (회원key: <?php echo $list['mb_idx']; ?>)</strong></td>
                            <td><?php echo $list['memo']; ?></td>
                            <td class="tac"><?php echo $list['regdate']; ?></td>
                            <td class="tac"><button type="button" class="delBtn btn1 small" data-idx="<?php echo $list['idx']; ?>" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 삭제 처리 하시겠습니까? => idx:<?php echo $list['idx']; ?>">삭제</button></td>
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
            </form>
        </div>
    </div>

</article>
