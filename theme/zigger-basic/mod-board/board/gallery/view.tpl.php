<!-- top source code -->
<div id="mod_board_top_source"><?php echo $top_source; ?></div>

<form id="board-readForm" name="board-readForm">
    <input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
    <input type="hidden" name="category" value="<?php echo $category; ?>" />
    <input type="hidden" name="read" value="<?php echo $read; ?>" />
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="where" value="<?php echo $where; ?>" />
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" />
    <input type="hidden" name="thisuri" value="<?php echo $thisuri; ?>" />
</form>

<div id="board-view">

    <div class="view-tit">
        <h3>
            <?php if ($is_category_show) { ?>
            <em><?php echo $view['category']; ?></em>
            <?php } ?>

            <?php echo $secret_ico; ?>
            <?php echo $view['subject']; ?>
        </h3>
        <ul class="info">
            <li>
                <?php if ($print_profileimg) { ?>
                <div class="board-mb-profileimg" style="background-image: url('<?php echo $print_profileimg; ?>');"></div>
                <?php } ?>
                <?php echo $print_writer; ?>
            </li>
            <li><strong>작성일</strong><?php echo $view['datetime']; ?></li>
            <li><strong>조회</strong><?php echo $view['view']; ?></li>
        </ul>
    </div>

    <table>
        <colgroup>
            <col style="width: 100px;" />
            <col style="width: auto;" />
        </colgroup>
        <tbody>
            <tr>
                <td class="article-wrap" colspan="2">

                    <div class="article">

                        <?php if ($is_img_show[1]) { ?>
                        <div class="img-wrap"><?php echo $print_imgfile[1]; ?></div>
                        <?php } ?>

                        <?php if ($is_img_show[2]) { ?>
                        <div class="img-wrap"><?php echo $print_imgfile[2]; ?></div>
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

                    <!-- 좋아요/싫어요 -->
                    <?php if ($is_likes_show) { ?>
                        <form <?php echo $this->likes_form(); ?>>
                            <input type="hidden" name="board_id" value="<?php echo $board_id; ?>" />
                            <input type="hidden" name="read" value="<?php echo $read; ?>" />
                            <input type="hidden" name="mode" value="" />

                            <button type="button" class="btn-likes">
                                <img src="<?php echo MOD_BOARD_THEME_DIR; ?>/images/likes-ico.png" alt="좋아요" />
                                <p id="board-likes-cnt"><?php echo $view['likes_cnt']; ?></p>
                            </button>
                            <button type="button" class="btn-unlikes">
                                <img src="<?php echo MOD_BOARD_THEME_DIR; ?>/images/unlikes-ico.png" alt="싫어요" />
                                <p id="board-unlikes-cnt"><?php echo $view['unlikes_cnt']; ?></p>
                            </button>
                        </form>
                    <?php } ?>

                </td>
            </tr>

            <?php if ($is_file_show[1]) { ?>
            <tr>
                <th>
                    첨부파일
                </th>
                <td class="fileinfo">
                    <?php echo $print_file_name[1]; ?>
                </td>
            </tr>
            <?php } ?>

            <?php if ($is_file_show[2]) { ?>
            <tr>
                <th>
                    첨부파일2
                </th>
                <td class="fileinfo">
                    <?php echo $print_file_name[2]; ?>
                </td>
            </tr>
            <?php } ?>

        </tbody>
    </table>

    <!-- comment -->
    <?php if ($is_comment_show) { ?>
    <div id="board-comment"></div>
    <?php } ?>

    <!-- 이전/다음 글 -->
    <?php if ($is_seeklist_show) { ?>
    <ul id="board-seek">
        <?php if ($view[0]['next_data']) { ?>
        <li>
            <strong>다음글 <i class="fas fa-caret-up"></i></strong>
            <a href="<?php echo $view[0]['next_data']['get_link']; ?>" class="sbj">
                <?php echo $view[0]['next_data']['subject']; ?>
                <span class="cmt"><?php echo $view[0]['next_data']['comment_cnt']; ?></span>
            </a>
            <span class="writer">
                <?php if ($view[0]['next_data']['profileimg']) { ?>
                <div class="board-mb-profileimg" style="background-image: url('<?php echo $view[0]['next_data']['profileimg']; ?>');"></div>
                <?php } ?>

                <?php echo $view[0]['next_data']['writer']; ?>
            </span>
            <span class="date" title="<?php echo $view[0]['next_data']['datetime']; ?>">
                <?php echo $view[0]['next_data']['date']; ?>
            </span>
        </li>
        <?php } ?>

        <?php if ($view[0]['prev_data']) { ?>
        <li>
            <strong>이전글 <i class="fas fa-caret-down"></i></strong>
            <a href="<?php echo $view[0]['prev_data']['get_link']; ?>" class="sbj">
                <?php echo $view[0]['prev_data']['subject']; ?>
                <span class="cmt"><?php echo $view[0]['prev_data']['comment_cnt']; ?></span>
            </a>
            <span class="writer">
                <?php if ($view[0]['prev_data']['profileimg']) { ?>
                <div class="board-mb-profileimg" style="background-image: url('<?php echo $view[0]['prev_data']['profileimg']; ?>');"></div>
                <?php } ?>

                <?php echo $view[0]['prev_data']['writer']; ?>
            </span>
            <span class="date" title="<?php echo $view[0]['prev_data']['datetime']; ?>">
                <?php echo $view[0]['prev_data']['date']; ?>
            </span>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>

    <!-- button -->
    <div class="btn-wrap">
        <div class="left">
            <?php echo $list_btn; ?>
        </div>
        <div class="right">
            <?php echo $delete_btn; ?>
            <?php echo $modify_btn; ?>
            <?php echo $reply_btn; ?>
        </div>
    </div>

</div>

<!-- 하단리스트 -->
<?php if ($is_ftlist_show) { ?>
<div id="board-ft-list"></div>
<?php } ?>

<!-- bottom source code -->
<div id="mod_board_bottom_source"><?php echo $bottom_source; ?></div>
