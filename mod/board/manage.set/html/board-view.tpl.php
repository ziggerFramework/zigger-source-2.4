<div id="sub-tit">
    <h2>게시글 조회</h2>
    <em><i class="fa fa-exclamation-circle"></i>작성된 게시글 상세 조회 및 코멘트 관리</em>
</div>

<!-- article -->
<article>
    <div id="board-view" class="view-wrap">

        <form id="board-readForm" name="board-readForm">
            <input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
            <input type="hidden" name="read" value="<?php echo $read; ?>" />
            <input type="hidden" name="thisuri" value="<?php echo $thisuri; ?>" />
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
            <input type="hidden" name="where" value="<?php echo $where; ?>" />
            <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" />
            <input type="hidden" name="category" value="<?php echo $category; ?>" />
            <input type="hidden" name="request" value="manage" />
        </form>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="view-tit tal">
                        <?php if ($is_category_show) { ?>
                        [<?php echo $view['category']; ?>]
                        <?php } ?>

                        <?php echo $secret_ico; ?>
                        <?php echo $view['subject']; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>작성자</th>
                    <td><?php echo $print_writer; ?></td>
                </tr>
                <tr>
                    <th>작성일</th>
                    <td><?php echo $view['datetime']; ?></td>
                </tr>
                <tr>
                    <th>조회수</th>
                    <td><?php echo $view['view']; ?></td>
                </tr>
                <tr>
                    <th>글 반응</th>
                    <td>좋아요 : <strong><?php echo $view['likes_cnt']; ?></strong> / 싫어요 : <strong><?php echo $view['unlikes_cnt']; ?></strong></td>
                </tr>
                
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($view['data_'.$i])) {
                ?>
                <tr>
                    <th>여분필드<?php echo $i; ?></th>
                    <td><?php echo $view['data_'.$i]; ?></td>
                </tr>
                <?php }} ?>

                <tr>
                    <td class="article-wrap" colspan="2">

                        <div class="article">

                            <?php foreach ($print_imgfile as $img) { ?>
                            <div class="img-wrap"><?php echo $img; ?></div>
                            <?php } ?>

                            <?php if ($is_article_show) { ?>
                            <div class="nostyle"><?php echo $view['article']; ?></div>
                            <?php }?>

                            <?php if ($is_dropbox_show) { ?>
                            <p class="drop-box">
                                현재 게시물은 <strong><?php echo $view['dregdate']; ?></strong> 에 삭제된 글입니다.<br />
                                답글 보호를 위해 답글이 달린 원글은 리스트에서 제거되지 않습니다.
                            </p>
                            <?php } ?>

                        </div>

                    </td>
                </tr>

                <?php foreach ($print_file_name as $key => $value) { ?>
                <tr>
                    <th>
                        첨부파일<?php echo $key + 1; ?>
                    </th>
                    <td class="fileinfo">
                        <?php echo $value; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- comment -->
        <?php if ($is_comment_show) { ?>
        <div id="board-comment"></div>
        <?php } ?>

        <div class="btn-wrap">
            <?php echo $list_btn; ?>
            <?php echo $delete_btn; ?>
            <?php echo $modify_btn; ?>
            <?php echo $reply_btn; ?>
        </div>
    </div>

</article>
