<?php
use Make\Database\Pdosql;

$stsql = new Pdosql();

$stsql->query(
    "
    select *
    from {$stsql->table("sitemap")}
    where char_length(caidx)=4 and visible='Y'
    order by caidx asc
    ", ''
);
$list_cnt = $stsql->getcount();

// make GNB
$SITEMAP = array();
$link_dir = PH_DIR.'/';

if ($list_cnt > 0) {
    do {
        $arr = $stsql->fetchs();

        $firstChar = substr($arr['href'], 0, 1);
        $arr['href'] = ($firstChar == '@') ? substr($arr['href'], 1) : $link_dir.$arr['href'];
        $arr['target'] = ($firstChar == '@') ? '_blank' : '_self';

        //depth 2
        $gnb_arr2 = array();
        if ($stsql->fetch('children') > 0) {

            $stsql2 = new Pdosql();

            $stsql2->query(
                "
                select *
                from {$stsql->table("sitemap")}
                where substr(caidx,1,4)=:col1 and char_length(caidx)=8 and visible='Y'
                order by caidx asc
                ",
                array(
                    $arr['caidx']
                )
            );

            if ($stsql2->getcount() > 0) {
                do {
                    $arr2 = $stsql2->fetchs();
                    
                    $firstChar = substr($arr2['href'], 0, 1);
                    $arr2['href'] = ($firstChar == '@') ? substr($arr2['href'], 1) : $link_dir.$arr2['href'];
                    $arr2['target'] = ($firstChar == '@') ? '_blank' : '_self';

                    //depth 3
                    $gnb_arr3 = array();

                    if ($stsql2->fetch('children') > 0) {
                        $stsql3 = new Pdosql();

                        $stsql3->query(
                            "
                            select *
                            from {$stsql->table("sitemap")}
                            where substr(caidx,1,8)=:col1 and char_length(caidx)=12 and visible='Y'
                            order by caidx asc
                            ",
                            array(
                                $arr2['caidx']
                            )
                        );

                        if ($stsql3->getcount() > 0) {
                            do {
                                $arr3 = $stsql3->fetchs();

                                $firstChar = substr($arr3['href'], 0, 1);
                                $arr3['href'] = ($firstChar == '@') ? substr($arr3['href'], 1) : $link_dir.$arr3['href'];
                                $arr3['target'] = ($firstChar == '@') ? '_blank' : '_self';

                                $gnb_arr3[] = $arr3;

                            } while ($stsql3->nextRec());
                        }
                    }

                    $arr2['3d'] = $gnb_arr3;

                    $gnb_arr2[] = $arr2;

                } while ($stsql2->nextRec());
            }
        }
        $arr['2d'] = $gnb_arr2;

        $SITEMAP[] = $arr;

    } while ($stsql->nextRec());
}

// make LNB
$LNB_SITEMAP = array();

$sitemap_idx = null;

for ($i = 0;$i < count($SITEMAP);$i++) {
    foreach ($SITEMAP[$i] as $key => $value) {
        if ($key == 'idx' && isset($NAVIGATOR[0]['idx']) && $value == $NAVIGATOR[0]['idx']) $sitemap_idx = $i;
    }
}

if (isset($sitemap_idx)) $LNB_SITEMAP = $SITEMAP[$sitemap_idx];
