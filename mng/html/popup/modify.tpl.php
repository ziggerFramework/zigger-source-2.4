<div id="sub-tit">
    <h2>팝업 정보 관리</h2>
    <em><i class="fa fa-exclamation-circle"></i>팝업 정보 확인 및 관리</em>
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
                    <th colspan="2" class="tal">팝업 기본설정</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>팝업 id</th>
                    <td>
                        <strong><?php echo $write['id']; ?></strong>
                        <span class="tbl_sment">팝업 id는 변경 불가합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>팝업 제목</th>
                    <td>
                        <input type="text" name="title" class="inp w50p" title="팝업 제목" value="<?php echo $write['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>접속 링크</th>
                    <td>
                        <input type="text" name="link" class="inp w100p" title="접속 링크" value="<?php echo $write['link']; ?>" />
                        <span class="tbl_sment">팝업 클릭시 위 링크로 이동합니다.<br />링크를 설정하면 팝업 내용에서 설정한 링크는 반영되지 않습니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>링크 target</th>
                    <td>
                        <select name="link_target" class="inp">
                            <option value="_self" <?php if($write['link_target']=="_self"){ echo "selected"; }?>>현재창 (_self)</option>
                            <option value="_blank" <?php if($write['link_target']=="_blank"){ echo "selected"; }?>>새창 (_blank)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>폭(width)</th>
                    <td>
                        <input type="text" name="width" class="inp w50" title="가로 폭(width)" value="<?php echo $write['width']; ?>" /> px
                    </td>
                </tr>
                <tr>
                    <th>높이(height)</th>
                    <td>
                        <input type="text" name="height" class="inp w50" title="높이(height)" value="<?php echo $write['height']; ?>" /> px
                    </td>
                </tr>
                <tr>
                    <th>팝업 top 위치</th>
                    <td>
                        <input type="text" name="pos_top" class="inp w50" title="팝업 top 위치" value="<?php echo $write['pos_top']; ?>" /> px
                    </td>
                </tr>
                <tr>
                    <th>팝업 left 위치</th>
                    <td>
                        <input type="text" name="pos_left" class="inp w50" title="팝업 left 위치" value="<?php echo $write['pos_left']; ?>" /> px
                    </td>
                </tr>
                <tr>
                    <th>노출 대상</th>
                    <td>
                        <select name="level_from" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==$write['level_from']){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <select name="level_to" class="inp">
                            <?php for($i=1;$i<=10;$i++){ ?>
                            <option value="<?php echo $i; ?>" <?php if($i==$write['level_to']){ echo "selected"; }?>><?php echo $i; ?> (<?php echo $MB['type'][$i]; ?>)</option>
                            <?php } ?>
                        </select>
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                        <span class="tbl_sment">작은 숫자의 레벨 부터 입력. ex) 1 ~ 10</span>
                    </td>
                </tr>
                <tr>
                    <th>노출 기간</th>
                    <td>
                        <input type="text" name="show_from" class="inp" title="팝업 노출 시작일" value="<?php echo $write['show_from']; ?>" datepicker />
                        &nbsp;&nbsp;부터&nbsp;&nbsp;
                        <input type="text" name="show_to" class="inp" title="팝업 노출 종료일" value="<?php echo $write['show_to']; ?>" datepicker />
                        &nbsp;&nbsp;까지&nbsp;&nbsp;
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>

        <table class="table1">
            <thead>
                <tr>
                    <th colspan="2" class="tal">팝업 내용 설정</th>
                </tr>
                <tr>
                    <th class="tal">PC 팝업 내용</th>
                    <th class="tal">모바일 팝업 내용</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <textarea id="html" name="html" title="PC 팝업 내용"><?php echo $write['html']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('html');</script>
                    </td>
                    <td>
                        <textarea id="mo_html" name="mo_html" title="모바일 팝업 내용"><?php echo $write['mo_html']; ?></textarea>
                        <script type="text/javascript">CKEDITOR.replace('mo_html');</script>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrap">
            <div class="center">
                <a href="#" class="btn2 mr30" data-form-before-confirm="다시 복구할 수 없습니다. 정말로 삭제 처리 하시겠습니까? => mode:del"><i class="fa fa-trash-alt"></i>팝업 삭제</a>
                <a href="./result<?php echo $manage->lnk_def_param(); ?>" class="btn2">리스트</a>
                <button type="submit" class="btn1"><i class="fa fa-check"></i>저장</button>
            </div>
        </div>
    </form>

</article>
