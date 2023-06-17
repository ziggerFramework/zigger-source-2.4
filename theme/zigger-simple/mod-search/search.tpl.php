<div id="sub-tit">
    <h2>'<?php echo $keyword; ?>' 에 대한 통합검색 결과</h2>
</div>

<form action="" class="sch-form">
    <legend>통합검색</legend>
    <fieldset>
        <input type="text" name="keyword" class="inp" value="<?php echo $keyword; ?>" />
        <button type="submit" class="sbm">검색</button>
    </fieldset>
</form>

<?php
$result_count = 0;
foreach ($print_arr as $list) {
    if (isset($list['data']) && count($list['data']) > 0) {
?>
<div class="sch-box">
    <div class="sch-tit">
        <h3><?php echo $list['title']; ?></h3>
        <a href="<?php echo $list[0]['href']; ?>" class="more">검색 결과 더 보기 <i class="fas fa-chevron-right"></i></a>
    </div>
    <ul class="sch-result">
        <?php foreach ($list['data'] as $data) { ?>
        <li>
            <?php if (isset($data['subject'])) { ?>
            <a href="<?php echo $list['href']; ?><?php echo $data['link']; ?>" class="sbj"><?php echo $data['subject']; ?></a>
            <?php } ?>

            <?php if (isset($data['article'])) { ?>
            <a href="<?php echo $list['href']; ?><?php echo $data['link']; ?>" class="txt"><?php echo $data['article']; ?></a>
            <?php } ?>

            <?php if (isset($data['info'])) { ?>
            <ul class="inf">
                <?php if ($data['info']['writer']) { ?><li>작성자 : <?php echo $data['info']['writer']; ?></li><?php } ?>
                <?php if ($data['info']['regdate']) { ?><li>작성일 : <?php echo $data['info']['regdate']; ?></li><?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php
        $result_count++;
    }
}
?>

<?php if ($result_count < 1) { ?>
<div class="search-no-data">검색 결과가 존재하지 않습니다. 다른 검색어로 검색해보세요.</div>
<?php } ?>
