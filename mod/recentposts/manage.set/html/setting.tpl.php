<div id="sub-tit">
    <h2>Recent Posts</h2>
    <em><i class="fa fa-exclamation-circle"></i>신규게시글 레코딩 설정 및 관리</em>
</div>

<!-- article -->
<article>

    <p class="article-wait">
        <i class="fa fa-info-circle"></i>
        Recent Posts에 기록될 게시판을 지정하세요.
        <em>게시판 지정 시점부터 신규게시글(Recent Posts) 레코딩이 시작됩니다.</em>
    </p>

    <form <?php echo $this->form(); ?>>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">기록될 게시판 지정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>게시판 id 지정</th>
                    <td>
                        <input type="text" name="boards" class="inp w100p" title="게시판 id" value="<?php echo $write['boards']; ?>" />
                        <span class="tbl_sment">파이프(|)로 구분하여 다수 게시판 지정 (ex: freeboard|news)<br />지정된 시점부터 Recent Posts 레코드에 기록되며, 이전에 작성된 게시글은 기록되지 않습니다.</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 설정완료</button>
            </div>
        </div>
    </form>

</article>
