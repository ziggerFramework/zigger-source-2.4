<div id="sub-tit">
    <h2>배너 정보 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>배너 정보 확인 및 관리</em>
</div>

<!-- article -->
<article>
    <form <?php echo $this->form(); ?>>
        <?php echo $manage->print_hidden_inp(); ?>
        <input type="hidden" name="mode" value="mod" />
        <input type="hidden" name="idx" value="<?php echo $write['idx']; ?>" />


        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">배너 기본설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>배너 key</th>
                    <td>
                        <input type="text" name="key" class="inp" title="배너 key" value="<?php echo $write['bn_key']; ?>" />
                        <span class="tbl_sment">타 배너와 중복된 key를 입력하여 다중노출 가능<br />영어, 숫자 조합으로 입력<br />최소 3자~최대 15자 까지 입력</span>
                    </td>
                </tr>
                <tr>
                    <th>배너 순서</th>
                    <td>
                        <input type="text" name="zindex" class="inp" title="배너 순서" placeholder="1" value="<?php echo $write['zindex']; ?>" />
                        <span class="tbl_sment">타 배너와 key가 같은 경우 우선 순위 지정<br />숫자가 작을 수록 먼저 노출</span>
                    </td>
                </tr>
                <tr>
                    <th>배너 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="배너 제목" value="<?php echo $write['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>배너 링크</th>
                    <td>
                        <input type="text" name="link" class="inp w100p" title="배너 링크" value="<?php echo $write['link']; ?>" />
                        <span class="tbl_sment">배너 클릭시 위 링크로 이동합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>링크 target</th>
                    <td>
                        <select name="link_target" class="inp">
                            <option value="_self" <?php if ($write['link_target']=="_self") { echo "selected"; }?>>현재창 (_self)</option>
                            <option value="_blank" <?php if ($write['link_target']=="_blank") { echo "selected"; }?>>새창 (_blank)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>클릭 수</th>
                    <td>
                        <strong><?php echo $write['hit']; ?></strong> 회 클릭
                    </td>
                </tr>
                <tr>
                    <th>PC 배너 이미지</th>
                    <td>
                        <input type="file" name="pc_img" />

                        <?php if ($is_pc_img_show) { ?>
                        <dl class="fileview no-dt">
                            <dd>
                                <strong>등록된 파일</strong>
                                <a href="<?php echo $write[0]['pc_img']['replink']; ?>" target="_blank"><?php echo $write[0]['pc_img']['orgfile']; ?></a>
                                <img src="<?php echo $write[0]['pc_img']['replink']; ?>" />
                            </dd>
                        </dl>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>모바일 배너 이미지</th>
                    <td>
                        <input type="file" name="mo_img" />

                        <?php if ($is_mo_img_show) { ?>
                        <dl class="fileview no-dt">
                            <dd>
                                <strong>등록된 파일</strong>
                                <a href="<?php echo $write[0]['mo_img']['replink']; ?>" target="_blank"><?php echo $write[0]['mo_img']['orgfile']; ?></a>
                                <img src="<?php echo $write[0]['mo_img']['replink']; ?>" />
                            </dd>
                        </dl>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="#" class="btn2 mr30" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 삭제 처리 하시겠습니까? => mode:del"><i class="fa fa-trash-alt"></i>배너 삭제</a>
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
