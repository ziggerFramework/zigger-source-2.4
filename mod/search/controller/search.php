<?php
namespace Module\Search;

use Corelib\Func;
use Corelib\Method;
use Make\Database\Pdosql;

//
// Module Controller
// ( Search )
//
class Search extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_SEARCH_THEME_PATH.'/search.tpl.php');
    }

    public function func()
    {
        function exp_keywords($keyword) {
            $exp = explode(' ', $keyword);

            $key_arr = array();

            for ($i = 0; $i < count($exp); $i++) {
                $key_arr[$i] = $exp[$i];
            }

            return $key_arr;
        }

        function get_module_contents($keyword)
        {
            $sql = new Pdosql();

            $where = '';

            foreach (exp_keywords($keyword) as $key => $value)
            {
                $or = ($key > 0) ? ' or ' : '';
                $where .= $or.'html like \'%'.addslashes($value).'%\'';
            }

            $sql->query(
                "
                select *
                from {$sql->table("mod:contents")}
                where ({$where})
                ", []
            );

            $return = ($sql->getcount() > 0) ? array() : null;

            if ($sql->getcount() > 0) {
                $arr = array();
                do {
                    $sql->specialchars = 0;

                    $arr['article'] = Func::strcut(strip_tags($sql->fetch('html')), 0, 250);
                    $arr['link'] = '';

                    $return[] = $arr;

                } while($sql->nextRec());

            }

            return $return;
        }

        function get_module_board($keyword, $board, $limit)
        {
            $sql = new Pdosql();

            if (!$sql->table_exists('mod:board_data_'.$board)) return null;

            $where = array('', '', '');

            foreach (exp_keywords($keyword) as $key => $value) {
                $or = ($key > 0) ? ' or ' : '';
                $where[0] .= $or.' subject like \'%'.addslashes($value).'%\'';
                $where[1] .= $or.' article like \'%'.addslashes($value).'%\'';
                $where[2] .= $or.' writer like \'%'.addslashes($value).'%\'';
            }

            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_{$board}")}
                where ({$where[0]}) or ({$where[1]}) or ({$where[2]}) and dregdate is null
                order by regdate desc
                limit 0, {$limit}
                ", []
            );

            $return = ($sql->getcount() > 0) ? array() : null;

            if ($sql->getcount() > 0) {
                do {
                    $arr['subject'] = $sql->fetch('subject');
                    $arr['link'] = '/'.$sql->fetch('idx');
                    $arr['info']['writer'] = $sql->fetch('writer');
                    $arr['info']['regdate'] = Func::date($sql->fetch('regdate'));

                    $sql->specialchars = 0;
                    $arr['article'] = Func::strcut(strip_tags($sql->fetch('article')), 0, 250);

                    $return[] = $arr;

                } while($sql->nextRec());

            }

            return $return;
        }
    }

    public function make()
    {
        $sql = new Pdosql();

        $req = Method::request('get', 'keyword');

        $req['keyword'] = trim(urldecode($req['keyword']));

        $sql->query(
            "
            select *
            from {$sql->table("mod:search")}
            where opt is not null and href is not null
            order by caidx asc
            ", []
        );

        $print_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $arr = $sql->fetchs();

                $mod_arr = array();
                $opt_exp = explode('|', $arr['opt']);

                // module type
                $mod_arr['modue'] = $opt_exp[0];
                $mod_arr['title'] = $arr['title'];

                // 더보기 링크 생성
                $mod_arr['href'] = PH_DOMAIN.'/'.$arr['href'];
                $mod_arr[0]['href'] = PH_DOMAIN.'/'.$arr['href'];

                if ($opt_exp[0] == 'board') $mod_arr[0]['href'] = $mod_arr['href'].'?keyword='.urlencode($req['keyword']);

                // 모듈별 Database 처리
                switch ($opt_exp[0]) {
                    case 'contents' :
                        $mod_arr['data'] = get_module_contents($req['keyword']);
                        break;

                    case 'board' :
                        $mod_arr['data'] = get_module_board($req['keyword'], $opt_exp[1], $opt_exp[2]);
                        break;
                }

                $print_arr[] = $mod_arr;

            } while ($sql->nextRec());

        }

        $this->set('keyword', $req['keyword']);
        $this->set('print_arr', $print_arr);

    }

}
