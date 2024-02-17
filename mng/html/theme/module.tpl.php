<div id="sub-tit">
    <h2>설치된 모듈</h2>
    <em><i class="fa fa-exclamation-circle"></i>설치된 모듈 확인 (모듈 설치 경로 : /mod/)</em>
</div>

<!-- sorting -->
<div id="list-sort">
    <ul>
        <li><a href="module"><em>전체모듈</em><p><?php echo $module_total; ?></p></a></li>
    </ul>
</div>

<!-- article -->
<article>

    <ul class="mod-list">
        <?php foreach ($print_arr as $list) { ?>
        <li>
            <span class="tit"><?php echo $list['name']; ?> <em>(<?php echo $list['module']; ?>)</em></span>
            <ul class="info">
                <li><strong>제작자</strong><p><?php echo $list['developer']; ?></p></li>
                <li><strong>버전</strong><p><?php echo $list['version']; ?></p></li>
                <li><strong>제작일</strong><p><?php echo $list['develDate']; ?></p></li>
                <li><strong>업데이트일</strong><p><?php echo $list['updateDate']; ?></p></li>
                <li><strong>제작자 web</strong><a href="<?php echo $list['website']; ?>" target="_blank"><p><?php echo $list['website']; ?></p></a></li>
            </ul>
            <?php if (isset($list['golink'])) { ?>
            <div class="btn-wrap mt0 mb10">
                <a href="<?php echo PH_MANAGE_DIR; ?>/mod/<?php echo $list['module']; ?>/<?php echo $list['golink']; ?>" class="btn2 small">모듈 보기</a>
            </div>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>

</article>
