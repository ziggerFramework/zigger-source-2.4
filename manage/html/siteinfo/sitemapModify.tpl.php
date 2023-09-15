<input type="hidden" name="idx" value="<?php echo $write['idx']; ?>" />

<table class="table1">
    <thead>
        <tr>
            <th colspan="2" class="tal">카테고리 세부 설정</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>카테고리 key</th>
            <td>
                <strong><?php echo $write['idx']; ?></strong>
                <span class="tbl_sment">page에서 카테고리 타이틀, navigator 출력을 위해 활용</span>
            </td>
        </tr>
        <tr>
            <th>카테고리명</th>
            <td>
                <input type="text" name="title" class="inp" title="카테고리명" value="<?php echo $write['title']; ?>" />
            </td>
        </tr>
        <tr>
            <th>연결 page</th>
            <td>
                <?php echo PH_DOMAIN; ?>/ <input type="text" name="href" class="inp w50p" title="연결 page" value="<?php echo $write['href']; ?>" />
                <span class="tbl_sment">
                    '/app/' 경로에 위치한 경로 입력.<br />
                    '/app/folder/file.php' 에 {Hello} Class 로 정의한 경우 - '/folder/file/hello' 입력<br />
                </span>
                <span class="tbl_sment">
                    외부링크로 설정 하려는 경우 앞에 '@'를 붙여 외부 URL 입력(외부링크는 클릭시 새창으로 연결)<br />
                    ex) @https://www.zigger.net
                </span>
            </td>
        </tr>
        <tr>
            <th>노출여부</th>
            <td>
                <label class="mr10"><input type="radio" name="visible" value="Y" <?php echo $visible['Y']; ?> /> 카테고리 노출함</label>
                <label><input type="radio" name="visible" value="N" <?php echo $visible['N']; ?> /> 카테고리 노출 안함</label>
                <span class="tbl_sment">카테고리를 노출을 비활성화 하는 경우 하위에 속한 카테고리의 노출도 비활성화 됩니다.</span>
            </td>
        </tr>
    </tbody>
</table>

<div class="btn-wrap">
    <div class="center">
        <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
    </div>
</div>
