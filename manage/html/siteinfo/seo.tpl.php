<div id="sub-tit">
    <h2>검색엔진 최적화</h2>
    <em><i class="fa fa-exclamation-circle"></i>검색엔진 최적화를 위한 META Tag 및 Script 코드 관리</em>
</div>

<!-- article -->
<article>

    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>

        <?php echo $print_target[0]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">Open Graph</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>og:type</th>
                    <td>
                        <input type="text" name="og_type" class="inp" title="og:type" value="<?php echo $write['og_type']; ?>" placeholder="website" />
                        <span class="tbl_sment">기본값: website</span>
                    </td>
                </tr>
                <tr>
                    <th>og:title</th>
                    <td>
                        <input type="text" name="og_title" class="inp" title="og:title" value="<?php echo $write['og_title']; ?>" />
                        <span class="tbl_sment">SNS, 메신저에 웹사이트 링크 공유시 노출되는 공유 제목</span>
                    </td>
                </tr>
                <tr>
                    <th>og:description</th>
                    <td>
                        <input type="text" name="og_description" class="inp w100p" title="og:description" value="<?php echo $write['og_description']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>og:url</th>
                    <td>
                        <input type="text" name="og_url" class="inp" title="og:url" value="<?php echo $write['og_url']; ?>" placeholder="<?php echo $CONF['domain']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>og:image</th>
                    <td>
                        <input type="file" name="og_image" />

                        <?php if ($is_og_image_show) { ?>
                        <dl class="fileview">
                            <dt><img src="<?php echo $write[0]['og_image']['replink']; ?>" /></dt>
                            <dd>
                                <strong>등록된 파일</strong>
                                <a href="<?php echo $write[0]['og_image']['replink']; ?>" target="_blank"><?php echo $write[0]['og_image']['orgfile']; ?></a>
                                <label class="ml10"><input type="checkbox" name="og_image_del" value="checked" /> 삭제</label>
                            </dd>
                        </dl>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[1]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">검색엔진 Webmaster Key</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>구글</th>
                    <td>
                        <input type="text" name="google_verific" class="inp" title="구글 검색엔진 인증키" value="<?php echo $write['google_verific']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>네이버</th>
                    <td>
                        <input type="text" name="naver_verific" class="inp" title="네이버 검색엔진 인증키" value="<?php echo $write['naver_verific']; ?>" />
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[2]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">Robots.txt 설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Robots Rule</th>
                    <td>
                        <textarea name="robotrule" title="Robots.txt Rule" style="height: 300px;"><?php echo $write['robotrule']; ?></textarea>
                        <span class="tbl_sment">robots.txt 파일이 입력한 내용으로 자동 생성됨</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>

        <?php echo $print_target[3]; ?>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">기타 head 소스코드 삽입</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Script</th>
                    <td>
                        <textarea name="script" title="Script 소스코드"><?php echo $write['script']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>Meta tag</th>
                    <td>
                        <textarea name="meta" title="Meta tag"><?php echo $write['meta']; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <button type="submit" class="btn1"><i class="fa fa-check"></i> 변경완료</button>
            </div>
        </div>
    </form>

</article>
