<?php
use \Corelib\Func;
use \Make\Database\Pdosql;

///
// Module Fetch Controller
// ( recentposts_latest_fetch )
//
class Recentposts_latest_fetch extends \Controller\Make_Controller {

    static private $called_func = 0;

    public function init()
    {
        global $FETCH_CONF;

        if (self::$called_func == 0) $this->recentposts_latest_func();

        $lat_skin = MOD_RECENTPOSTS_THEME_PATH.'/latest/'.$FETCH_CONF['theme'].'/latest.tpl.php';
        if (!file_exists($lat_skin)) Func::core_err('최근게시물 테마 파일이 존재하지 않습니다. : \''.$FETCH_CONF['theme'].'\'', false);

        $this->layout()->view($lat_skin);
    }

    public function recentposts_latest_func()
    {
        // 게시글 링크
        function recentposts_get_link($list)
        {
            global $FETCH_CONF;

            if (!isset($FETCH_CONF['uri'][$list['board_id']])) Func::core_err('uri 옵션이 누락되었거나, 삭제된 게시판의 게시글이 포함되어 있습니다. : \''.$list['board_id'].'\'', false);
            return (isset($FETCH_CONF['uri'][$list['board_id']])) ? $FETCH_CONF['uri'][$list['board_id']].'/'.$list['bo_idx'] : '';
        }

        // 제목
        function recentposts_print_subject($list)
        {
            global $FETCH_CONF;

            if (!$list['subject']) return '삭제 되었거나, 제목이 설정되지 않은 게시글입니다.';
            return Func::strcut($list['subject'], 0, $FETCH_CONF['subject']);
        }

        // 내용
        function recentposts_print_article($list)
        {
            global $FETCH_CONF;

            if (!$list['article']) return '삭제 되었거나, 내용이 설정되지 않은 게시글입니다.';
            $html = Func::strcut(strip_tags(Func::deHtmlspecialchars($list['article'])), 0, $FETCH_CONF['article']);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = preg_replace('/\s+/', ' ', $html);
            return $html;
        }

        // 댓글 갯수
        function recentposts_comment_cnt($list)
        {
            $sql = new Pdosql();
            
            if (!$sql->table_exists("mod:board_cmt_".$list['board_id'])) return '';

            $qry = $sql->query(
                "
                select count(*) as comment_cnt
                from {$sql->table("mod:board_cmt_".$list['board_id'])}
                where bo_idx={$list['bo_idx']}
                ", []
            );
            return ($sql->fetch('comment_cnt') > 0) ? Func::number($sql->fetch('comment_cnt')) : '';
        }

        // 썸네일 추출
        function recentposts_thumbnail($list)
        {
            if (!$list['article']) return SET_BLANK_IMG;
            
            // 본문내 첫번째 이미지 태그를 추출
            preg_match(REGEXP_IMG,Func::htmldecode($list['article']), $match);

            // 썸네일의 파일 타입을 추출
            $file_type = array();

            for ($i = 1; $i <= 2; $i++) {
                $file_type[$i] = Func::get_filetype($list['file'.$i]);
            }

            // 조건에 따라 썸네일 HTML코드 리턴
            for ($i=1; $i <= sizeof($file_type); $i++) {
                if (Func::chkintd('match', $file_type[$i], SET_IMGTYPE)) $tmb = $list['file'.$i];
            }

            if (isset($tmb)) {
                $fileinfo = Func::get_fileinfo($tmb);
                $tmb = ($fileinfo['storage'] == 'Y') ? $fileinfo['replink'] : MOD_BOARD_DATA_DIR.'/'.$list['board_id'].'/thumb/'.$tmb;

            } else if (isset($match[0])) {
                $tmb = $match[1];
            }

            if (!isset($tmb)) $tmb = SET_BLANK_IMG;

            return $tmb;
        }

        self::$called_func = 1;
    }

    public function make()
    {
        global $FETCH_CONF, $boardinfo, $orderby;

        $sql = new Pdosql();

        $continue = true;

        // 옵션 값 검사
        if (!isset($FETCH_CONF['limit']) || !$FETCH_CONF['limit'] || $FETCH_CONF['limit'] < 1) {
            $continue = Func::core_err('최근게시물 limit 옵션이 올바르지 않습니다. : \''.$FETCH_CONF['limit'].'\'', false);
        }
        if (!isset($FETCH_CONF['orderby']) || !$FETCH_CONF['orderby']) $FETCH_CONF['orderby'] = 'recent';
        if (!in_array($FETCH_CONF['orderby'], array('recent', 'view'))) {
            $continue = Func::core_err('최근게시물 orderby 옵션이 올바르지 않습니다. : \''.$FETCH_CONF['orderby'].'\'', false);
        }
        if (!isset($FETCH_CONF['title']) || !$FETCH_CONF['title']) $FETCH_CONF['title'] = 'Recent Posts';
        if (!isset($FETCH_CONF['subject']) || !$FETCH_CONF['subject']) $FETCH_CONF['subject'] = 30;
        if (!isset($FETCH_CONF['article']) || !$FETCH_CONF['article']) $FETCH_CONF['article'] = 50;
        if (!isset($FETCH_CONF['img-width']) || !$FETCH_CONF['img-width']) $FETCH_CONF['img-width'] = 150;
        if (!isset($FETCH_CONF['img-height']) || !$FETCH_CONF['img-height']) $FETCH_CONF['img-height'] = 150;
        if ($continue === true) {

            // 게시물 가져옴
            switch ($FETCH_CONF['orderby']) {
                case 'recent' :
                    $orderby = 'regdate desc, idx desc';
                    break;

                case 'view' :
                    $orderby = 'view desc, regdate desc';
                    break;
            }

            $sql->query(
                "
                select *
                from {$sql->table("mod:recentposts")}
                order by $orderby
                limit {$FETCH_CONF['limit']}
                ", []
            );

            $lat_cnt = $sql->getcount();
            $print_arr = array();

            if ($lat_cnt > 0) {
                do {
                    $lat_arr = $sql->fetchs();

                    $lat_arr[0]['get_link'] = recentposts_get_link($lat_arr);
                    $lat_arr[0]['print_subject'] = recentposts_print_subject($lat_arr);
                    $lat_arr[0]['print_article'] = recentposts_print_article($lat_arr);
                    $lat_arr[0]['thumbnail'] = recentposts_thumbnail($lat_arr);
                    $lat_arr['date'] = Func::date($lat_arr['regdate']);
                    $lat_arr['img-width'] = $FETCH_CONF['img-width'];
                    $lat_arr['img-height'] = $FETCH_CONF['img-height'];
                    $lat_arr[0]['comment_cnt'] = recentposts_comment_cnt($lat_arr);

                    $print_arr[] = $lat_arr;

                } while ($sql->nextRec());
            }

        }

        if ($continue === true) {
            $this->set('print_arr', $print_arr);
            $this->set('board_title', $FETCH_CONF['title']);

        } else {
            $this->set('print_arr', '');
        }

        $this->set('get_board_link', '');
    }
}