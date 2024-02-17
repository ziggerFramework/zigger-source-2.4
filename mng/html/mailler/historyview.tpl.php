<div id="sub-tit">
    <h2>발송 메일 확인</h2>
    <em><i class="fa fa-exclamation-circle"></i>발송된 메일 정보 확인</em>
</div>

<!-- article -->
<article class="view">

    <div class="view-wrap">
        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">메일 정보</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($is_level_show) { ?>
                <tr>
                    <th>수신 범위</th>
                    <td>
                        <?php echo $print_level; ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($is_to_mb_show) { ?>
                <tr>
                    <th>수신 회원 ID</th>
                    <td>
                        <?php echo $view['to_mb']; ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th>발송일</th>
                    <td>
                        <?php echo $view['regdate']; ?>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td>
                        <?php echo $view['subject']; ?>
                    </td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td class="nostyle">
                        <?php echo $view['html']; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="./history<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
            </div>
        </div>

    </div>

</article>
