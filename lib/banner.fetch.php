<?php
use Corelib\Func;
use Make\Database\Pdosql;

class Banner_fetch
{

    public function init()
    {
        global $CONF, $FETCH_CONF;

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("banner")}
            where bn_key=:col1
            order by zindex asc
            ",
            array(
                $FETCH_CONF['key']
            )
        );

        if ($sql->getcount() < 1) Func::core_err('Banner Key 가 존재하지 않습니다. : '.$FETCH_CONF['key'], false);
        $bn_html = '<ul>';

        if ($sql->getcount() < 1) return;

        do {
            $bn_arr = $sql->fetchs();

            $bn_html .= '
                <li alt="'.$bn_arr['title'].'" title="'.$bn_arr['title'].'">
            ';

            if ($bn_arr['link'] != '') {
                $bn_html .= '
                    <a href="'.PH_MANAGE_DIR.'/bannerhit.php?idx='.$bn_arr['idx'].'&key='.$bn_arr['bn_key'].'" target="'.$bn_arr['link_target'].'" class="link">
                ';
            }
            if (Func::chkdevice() == 'pc' || $CONF['use_mobile'] == 'N' ) {
                if ($bn_arr['pc_img']) {
                    $fileinfo = Func::get_fileinfo($bn_arr['pc_img']);

                    $bn_html .= '
                        <img src="'.$fileinfo['replink'].'" />
                    ';
                } else {
                    $bn_html .= '
                        <img src="'.PH_DIR.'/layout/images/blank-banner.jpg" width="100%" />
                    ';
                }
            } else {
                if ($bn_arr['mo_img']) {
                    $bn_html .= '
                        <img src="'.PH_DATA_DIR.'/manage/'.$bn_arr['mo_img'].'" />
                    ';
                } else {
                    $bn_html .= '
                        <img src="'.PH_DIR.'/layout/images/blank-banner.jpg" width="100%" />
                    ';
                }
            }
            if ($bn_arr['link'] != '') {
                $bn_html .= '
                    </a>
                ';
            }
            $bn_html .= '
                </li>
            ';

        } while ($sql->nextRec());

        $bn_html .= '
            </ul>
        ';

        if ($sql->getcount() > 0) echo $bn_html;
    }
}
