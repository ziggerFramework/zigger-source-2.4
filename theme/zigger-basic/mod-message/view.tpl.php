<div id="sub-tit">
    <h2><?php echo $MB['name']; ?>님의 메시지함</h2>
</div>

<?php $this->message_tab(); ?>

<table class="table_wrt">
    <caption>메시지 보기</caption>
    <colgroup>
        <col style="width: 150px;" />
        <col style="width: auto;" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="row">보낸 회원</th>
            <td>
                <strong><?php echo $view['f_mb_name'] ?></strong> (<?php echo $view['f_mb_id'] ?>)
            </td>
        </tr>
        <tr>
            <th scope="row">받는 회원</th>
            <td>
                <strong><?php echo $view['t_mb_name'] ?></strong> (<?php echo $view['t_mb_id'] ?>)
            </td>
        </tr>
        <tr>
            <th scope="row">발송</th>
            <td>
                <?php echo $view['regdate'] ?>
            </td>
        </tr>
        <tr>
            <th scope="row">읽음</th>
            <td>
                <?php
                if (!$view['chked']) {
                    echo '읽지않음';
                } else {
                    echo $view['chked'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">내용</th>
            <td>
                <?php echo $view['article'] ?>

                <?php if (count($history_arr) > 0) { ?>
                <ul class="msg-history">
                    <?php foreach ($history_arr as $list) { ?>
                    <li>
                        <div class="info">
                            <span class="name"><?php echo $list['mb_name'] ?> (<?php echo $list['mb_id'] ?>)</span>
                            <p>
                                <?php echo $list['article'] ?>
                            </p>
                            <span class="date"><?php echo $list['regdate'] ?></span>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </td>
        </tr>
    </tbody>
</table>

<div class="btn-wrap">
    <div class="center">
        <a href="<?php echo $view[0]['list-link'] ?>" class="btn2">메시지함 이동</a>

        <?php if ($refmode != 'sent') { ?>
        <button type="button" class="btn1" data-message-send="<?php echo $from_mb_id; ?>" data-message-send-reply="<?php echo $reply_parent_idx; ?>">답장하기</button>
        <?php } ?>
    </div>
</div>
