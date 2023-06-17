<form <?php echo $this->pass_form(); ?>>
    <input type="hidden" name="s_mode" value="<?php echo $mode; ?>" />
    <input type="hidden" name="s_wrmode" value="<?php echo $wrmode; ?>" />
    <input type="hidden" name="s_read" value="<?php echo $read; ?>" />
    <input type="hidden" name="s_page" value="<?php echo $page; ?>" />
    <input type="hidden" name="s_category" value="<?php echo $category; ?>" />
    <input type="hidden" name="s_where" value="<?php echo $where; ?>" />
    <input type="hidden" name="s_keyword" value="<?php echo $keyword; ?>" />

    <div id="board-pwd">
        <table>
            <thead>
                <tr>
                    <th>비밀번호를 입력하세요.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="password" name="s_password" class="inp" placeholder="비밀번호" />
                        <input type="submit" class="btn3 small" value="확인" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>
