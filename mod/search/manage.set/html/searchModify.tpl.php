<input type="hidden" name="idx" value="<?php echo $write['idx']; ?>" />

<table class="table1">
    <thead>
        <tr>
            <th colspan="2">검색 콘텐츠 세부 설정</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>검색 콘텐츠 타이틀</th>
            <td>
                <input type="text" name="title" class="inp" title="검색 콘텐츠 타이틀" value="<?php echo $write['title']; ?>" />
            </td>
        </tr>
        <tr>
            <th>검색 대상 모듈</th>
            <td>
                <select name="module" title="검색될 모듈" class="inp w100p">
                    <option value="">검색될 모듈을 선택하세요.</option>
                    <?php foreach ($get_modules as $key => $value) { ?>
                    <option value="<?php echo $value['type'].'|'.$value['id']; ?>" <?php if ($write[0]['module'] == $value['type'].'|'.$value['id']) { echo 'selected'; } ?>><?php echo $value['option-txt']; ?></option>
                    <?php } ?>
                </select>
                <span class="tbl_sment">통합검색 화면에서 검색될 모듈 선택</span>
            </td>
        </tr>
        <tr>
            <th>연결 page</th>
            <td>
                <?php echo PH_DOMAIN; ?>/ <input type="text" name="href" class="inp w50p" title="연결 page" value="<?php echo $write['href']; ?>" />
                <span class="tbl_sment">'/app/' 경로에 위치한 경로 입력.<br />'/app/folder/file.php' 의 {Hello} Class 로 정의한 경우 - '/folder/file/hello' 입력</span>
            </td>
        </tr>
        <tr>
            <th>노출될 검색결과 개수</th>
            <td>
                <select name="limit" title="검색 노출 개수" class="inp">
                    <?php for ($i=1; $i<=50; $i++) { ?>
                    <option value="<?php echo $i; ?>" <?php if ($write[0]['limit'] == $i) { echo 'selected'; } ?>><?php echo $i; ?>개 노출</option>
                    <?php } ?>
                </select>
                <span class="tbl_sment">통합검색 화면에서 노출될 검색 결과 개수 <br />콘텐츠 모듈인 경우 설정한 개수가 적용되지 않습니다.</span>
            </td>
        </tr>
    </tbody>
</table>

<div class="btn-wrap">
    <div class="center">
        <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
    </div>
</div>
