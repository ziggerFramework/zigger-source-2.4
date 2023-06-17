<?php
use Corelib\Func;
use Make\Database\Pdosql;

class Popup_fetch {

    public function init()
    {
        global $CONF, $MB, $FETCH_CONF;

        $sql = new Pdosql();

        // 팝업 정보
        $sql->query(
            "
            select *
            from {$sql->table("popup")}
            where show_from<now() and show_to>now()
            ", ''
        );

        if ($sql->getcount() > 0) {

            $arr = $sql->fetchs();

            // 팝업 html 출력
            $pop_html = '';
            $pop_idx = 0;

            do {
                $pop_arr = $sql->fetchs();
                $sql->specialchars = 0;
                $sql->nl2br = 0;
                $pop_arr['html'] = $sql->fetch('html');
                $pop_arr['mo_html'] = $sql->fetch('mo_html');

                $pop_html = "
                    <div id=\"ph-pop-{$pop_arr['idx']}\" class=\"ph-pop nostyle\" alt=\"{$pop_arr['title']}\" title=\"{$pop_arr['title']}\" style=\"top: {$pop_arr['pos_top']}px;left: {$pop_arr['pos_left']}px;\">
                ";

                if ($pop_arr['link'] != '') {
                    $pop_html .= "
                        <a href=\"{$pop_arr['link']}\" target=\"{$pop_arr['link_target']}\" class=\"link\"></a>
                    ";
                }

                if (Func::chkdevice() == "pc" || $CONF['use_mobile'] == "N") {
                    $pop_html .= "
                        <div class=\"pop-cont nostyle\" style=\"width: {$pop_arr['width']}px;height: {$pop_arr['height']}px;\">{$pop_arr['html']}</div>
                    ";

                } else {
                    $pop_html .= "
                        <div class=\"pop-cont\">{$pop_arr['mo_html']}</div>
                    ";
                }

                $pop_html .= "
                    <div class=\"pop-btn\">
                    <a href=\"#\" class=\"close\">팝업 닫기</a>
                    <a href=\"#\" class=\"close-today\" data-pop-idx=\"{$pop_arr['idx']}\">24시간 동안 닫기</a>
                    </div>
                ";

                $pop_html .= "
                    </div>
                ";

                if ($pop_arr['level_from'] <= $MB['level'] && $pop_arr['level_to'] >= $MB['level'] && !isset($_COOKIE['ph_pop_'.$pop_arr['idx']])) echo $pop_html;

                $pop_idx++;

            } while ($sql->nextRec());
        }
    }

}
