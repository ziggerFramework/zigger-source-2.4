<?php
use \Corelib\Func;
use \Make\Database\Pdosql;

//
// Controller for submit
// ( Latest_fetch )
//
class Latest_fetch extends \Controller\Make_Controller {

    static private $called_func = 0;

    public function init()
    {
        global $FETCH_CONF;

        if (self::$called_func == 0) $this->latest_func();

        $lat_skin = MOD_BOARD_THEME_PATH.'/latest/'.$FETCH_CONF['theme'].'/latest.tpl.php';
        if (!file_exists($lat_skin)) Func::core_err('최근게시물 테마 파일이 존재하지 않습니다. : \''.$FETCH_CONF['theme'].'\'', false);

        $this->layout()->view($lat_skin);
    }

    public function latest_func()
    {
        // 게시판 링크
        function get_board_link()
        {
            global $FETCH_CONF;

            return $FETCH_CONF['uri'];
        }

        // 게시글 링크
        function get_link($list)
        {
            global $FETCH_CONF;

            return $FETCH_CONF['uri'].'/'.$list['idx'];
        }

        // 제목
        function print_subject($list)
        {
            global $FETCH_CONF;

            return Func::strcut($list['subject'], 0, $FETCH_CONF['subject']);
        }

        // 내용
        function print_article($list)
        {
            global $FETCH_CONF;

            $html = Func::strcut(strip_tags(Func::deHtmlspecialchars($list['article'])), 0, $FETCH_CONF['article']);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = preg_replace('/\s+/', ' ', $html);
            return $html;
        }

        // 댓글 갯수
        function comment_cnt($list)
        {
            return ($list['comment_cnt'] > 0) ? Func::number($list['comment_cnt']) : '';
        }

        // 썸네일 추출
        function thumbnail($list)
        {
            global $CONF, $FETCH_CONF, $boardinfo;

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
                $tmb = ($fileinfo['storage'] == 'Y') ? $fileinfo['replink'] : MOD_BOARD_DATA_DIR.'/'.$FETCH_CONF['id'].'/thumb/'.$tmb;

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

        // 게시판 검사
        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$FETCH_CONF['id']}'
            ", []
        );

        $boardinfo = array();

        if ($sql->getcount() > 0) {
            do {
                $cfg = $sql->fetchs();
                $boardinfo[$cfg['cfg_key']] = $cfg['cfg_value'];

            } while($sql->nextRec());
        }

        $continue = true;

        if (!$FETCH_CONF['id'] || $sql->getcount() < 1) {
            Func::core_err('최근게시물 게시판 id가 올바르지 않습니다. : \''.$FETCH_CONF['id'].'\'', false);
            $continue = false;
        }

        // 옵션 값 검사
        if (!isset($FETCH_CONF['limit']) || !$FETCH_CONF['limit'] || $FETCH_CONF['limit'] < 1) {
            $continue = Func::core_err('최근게시물 limit 옵션이 올바르지 않습니다. : "'.$FETCH_CONF['limit'].'"', false);
            $continue = false;
        }
        if (!isset($FETCH_CONF['orderby']) || !$FETCH_CONF['orderby']) $FETCH_CONF['orderby'] = 'recent';
        if (!isset($FETCH_CONF['subject']) || !$FETCH_CONF['subject']) $FETCH_CONF['subject'] = 30;
        if (!isset($FETCH_CONF['article']) || !$FETCH_CONF['article']) $FETCH_CONF['article'] = 50;
        if (!isset($FETCH_CONF['img-width']) || !$FETCH_CONF['img-width']) $FETCH_CONF['img-width'] = 150;
        if (!isset($FETCH_CONF['img-height']) || !$FETCH_CONF['img-height']) $FETCH_CONF['img-height'] = 150;
        if (!isset($FETCH_CONF['uri']) || !$FETCH_CONF['uri']) {
            $continue = Func::core_err('uri 옵션이 올바르지 않습니다. : \''.$FETCH_CONF['uri'].'\'', false);
            $continue = false;
        }
        if ($continue === true) {

            //게시물 가져옴
            switch ($FETCH_CONF['orderby']) {
                case 'recent' :
                    $orderby = 'board.regdate desc, board.idx desc';
                    break;

                case 'view' :
                    $orderby = 'board.view desc, board.regdate desc';
                    break;

                case 'like' :
                    $orderby = 'likes_cnt desc, board.regdate desc';
                    break;
            }

            $sql->query(
                "
                select *,
                ( select count(*) from {$sql->table("mod:board_cmt_".$boardinfo['id'])} where bo_idx=board.idx ) comment_cnt,
                ( select count(*) from {$sql->table("mod:board_like")} where id='{$boardinfo['id']}' and data_idx=board.idx and likes>0 ) likes_cnt
                from {$sql->table("mod:board_data_".$boardinfo['id'])} board
                where board.rn=0 and board.dregdate is null
                order by $orderby
                limit {$FETCH_CONF['limit']}
                ", []
            );

            $lat_cnt = $sql->getcount();
            $print_arr = array();

            if ($lat_cnt > 0) {
                do {
                    $lat_arr = $sql->fetchs();

                    $lat_arr[0]['get_link'] = get_link($lat_arr);
                    $lat_arr[0]['print_subject'] = print_subject($lat_arr);
                    $lat_arr[0]['print_article'] = print_article($lat_arr);
                    $lat_arr[0]['thumbnail'] = thumbnail($lat_arr);
                    $lat_arr[0]['comment_cnt'] = comment_cnt($lat_arr);
                    $lat_arr['date'] = Func::date($lat_arr['regdate']);
                    $lat_arr['img-width'] = $FETCH_CONF['img-width'];
                    $lat_arr['img-height'] = $FETCH_CONF['img-height'];

                    $print_arr[] = $lat_arr;

                } while ($sql->nextRec());
            }

        }

        if ($continue === true) {
            $this->set('print_arr', $print_arr);
            $this->set('get_board_link', get_board_link());
            $this->set('board_title', $boardinfo['title']);

        } else {
            $this->set('print_arr', '');
            $this->set('get_board_link', '');
            $this->set('board_title', '');
        }
    }
}
