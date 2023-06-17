<?php
use Corelib\Func;
use Make\Database\Pdosql;

//
// Controller for display
// https://{domain}/rss
//
class Index {

    public function init()
    {
        global $CONF;

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='engine'
            ", []
        );

        $arr = array();

        do {
            $cfg = $sql->fetchs();
            $arr[$cfg['cfg_key']] = $cfg['cfg_value'];

            if ($cfg['cfg_key'] == 'rss_boards') {
                $sql->specialchars = 0;
                $sql->nl2br = 0;

                $arr[$cfg['cfg_key']] = $sql->fetch('cfg_value');
            }

        } while ($sql->nextRec());

        $rss_json = json_decode($arr['rss_boards'], true);
        $rss_borad = array();
        $rss_data = array();

        // rss 발행이 활성화 되어 있는지 검사
        if ($arr['use_rss'] != 'Y') Func::core_err('RSS 발행 기능이 활성화 되지 않았습니다.');

        if (!$rss_json) Func::core_err('RSS 발행 구문이 올바르지 않습니다.');

        // rss 추출할 게시판 검사
        foreach ($rss_json['rss'] as $key => $value) {
            $sql->query(
                "
                select *
                from {$sql->table("config")}
                where cfg_type='mod:board:config:{$value['board_id']}' and cfg_key='id' and cfg_value=:col1
                ", array(
                    $value['board_id']
                )
            );
            if ($sql->getcount() > 0) {
                $rss_borad[] = array(
                    'board_id' => $sql->fetch('cfg_value'),
                    'title' => $rss_json['rss'][$key]['title'],
                    'link' => $rss_json['rss'][$key]['link']
                );
            }
        }

        // 게시판별 최근글 20개씩 추출
        foreach ($rss_borad as $key => $value) {
            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_{$value['board_id']}")}
                order by regdate desc
                limit 20
                ", []
            );
            if ($sql->getcount() > 0) {
                do {
                    $sql->specialchars = 1;
                    $sql->nl2br = 0;

                    $rss_data[$sql->fetch('regdate')] = array(
                        'pubDate' => $sql->fetch('regdate'),
                        'article' => strip_tags($sql->fetch('memo')),
                        'link' => $value['link'].'/'.$sql->fetch('idx'),
                        'title' => $value['title'],
                        'data' => $sql->fetchs()
                    );

                } while ($sql->nextRec());
            }
        }

        // 가장 최근 순으로 역순 정렬 및 20개만 추출
        krsort($rss_data);
        $rss_data = array_slice($rss_data, 0, 50);

        // rss pubDate 생성
        $pubDate = date('r');
        if (count($rss_data) > 0) $pubDate = date('r', strtotime($rss_data[key($rss_data)]['pubDate']));

        // rss 구문 생성
        header('Content-type: text/xml');

        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<rss version="2.0">'.PHP_EOL;
        echo '<channel>'.PHP_EOL;
        echo '<title>'.$CONF['title'].'</title>'.PHP_EOL;
        echo '<link>'.$CONF['domain'].'</link>'.PHP_EOL;
        echo '<description>'.$CONF['description'].'</description>'.PHP_EOL;
        echo '<language>ko</language>'.PHP_EOL;
        echo '<pubDate>'.$pubDate.'</pubDate>'.PHP_EOL;
        echo '<generator>'.$CONF['title'].'</generator>'.PHP_EOL;
        echo '<ttl>100</ttl>'.PHP_EOL;
        echo '<managingEditor>'.$CONF['title'].'</managingEditor>'.PHP_EOL;

        foreach ($rss_data as $key => $value) {
            echo '<item>'.PHP_EOL;
            echo '<title>'.$value['data']['subject'].'</title>'.PHP_EOL;
            echo '<link>'.$value['link'].'</link>'.PHP_EOL;
            echo '<description>'.str_replace('&nbsp;', '', $value['data']['article']).'</description>'.PHP_EOL;
            echo '<category>'.$value['title'].'</category>'.PHP_EOL;
            echo '<author>'.$CONF['title'].'</author>'.PHP_EOL;
            echo '<guid isPermaLink="true">'.$value['link'].'</guid>'.PHP_EOL;
            echo '<comments>'.$value['link'].'</comments>'.PHP_EOL;
            echo '<pubDate>'.date('r', strtotime($value['pubDate'])).'</pubDate>'.PHP_EOL;
            echo '</item>'.PHP_EOL;
        }

        echo '</channel>'.PHP_EOL;
        echo '</rss>';

    }

}
