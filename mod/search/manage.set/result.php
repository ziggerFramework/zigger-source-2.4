<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Make\Database\Pdosql;
use Manage\ManageFunc;

//
// Controller for display
// https://{domain}/manage/mod/contents/result/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_SEARCH_PATH.'/manage.set/html/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function make()
    {

    }

    public function form()
    {

        $form = new \Controller\Make_View_Form();
        $form->set('id', 'searchListForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_SEARCH.'/result/searchList-submit');
        $form->run();
    }

    public function form2()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'searchModifyForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_SEARCH.'/result/searchModify-submit');
        $form->run();
    }

}

//
// Module Controller
// ( SearchList )
//
class SearchList extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_SEARCH_PATH.'/manage.set/html/searchList.tpl.php');
    }

    public function make()
    {
        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("mod:search")}
            where char_length(caidx)=4
            order by caidx asc
            ", []
        );
        $list_cnt = $sql->getcount();

        $print_arr = array();

        if ($list_cnt > 0) {
            do {

                $arr = $sql->fetchs();
                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('print_arr', $print_arr);

    }

}

//
// Controller for submit
// ( SearchList )
//
class SearchList_submit{

    public function init()
    {
        global $req;

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'type, idx, org_caidx, caidx, new_caidx');

        switch ($req['type']) {
            case 'add' :
                $this->get_add();
                break;

            case 'modify' :
                $this->get_modify();
                break;
        }
    }

    //
    // add
    //
    public function get_add()
    {
        global $req;

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("mod:search")}
            order by idx DESC
            limit 1
            ", []
        );

        $recent_idx = $sql->fetch('idx');

        ($recent_idx) ? $recent_idx++ : $recent_idx = 1;

        $sql->query(
            "
            insert into
            {$sql->table("mod:search")}
            (idx, caidx, title, children)
            values
            (:col1, :col2, :col3, :col4)
            ",
            array(
                $recent_idx, $req['new_caidx'], '새로운 통합검색 콘텐츠', 0
            )
        );

        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'ph_mod_search_manage.search_result(\'list_reload\');'
            )
        );
        Valid::turn();
    }

    //
    // modify
    //
    public function get_modify()
    {
        global $req, $where;

        $sql = new Pdosql();

        $where = '';

        if (count($req['idx']) < 1) {
            $where = 'idx!=-1';

        } else {
            for ($i = 0; $i < count($req['idx']); $i++) {
                $where .= ($i == 0) ? 'idx!=\''.$req['idx'][$i].'\'' : ' and idx!=\''.$req['idx'][$i].'\'';
            }
        }

        $sql->query(
            "
            delete
            from {$sql->table("mod:search")}
            where $where
            ", []
        );

        $children_count = array();

        for ($i = 0; $i < count($req['idx']); $i++) {
            $sql->query(
                "
                select COUNT(*) count
                from {$sql->table("mod:search")}
                where caidx like :col1
                ",
                array(
                    $req['org_caidx'][$i].'%'
                )
            );
            $children_count[$i] = $sql->fetch('count') - 1;
        }

        for ($i = 0; $i < count($req['idx']); $i++) {
            $sql->query(
                "
                update {$sql->table("mod:search")}
                set
                caidx=:col1, children=:col2
                where idx=:col3
                ",
                array(
                    $req['caidx'][$i], $children_count[$i], $req['idx'][$i]
                )
            );
        }

        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'ph_mod_search_manage.search_result(\'list_reload\');'
            )
        );
        Valid::turn();
    }
}

//
// Module Controller
// ( SearchModify )
//
class SearchModify extends \Controller\Make_Controller {

    public function init(){
        $this->layout()->view(MOD_SEARCH_PATH.'/manage.set/html/searchModify.tpl.php');
    }

    public function func()
    {
        function get_modules()
        {
            $sql = new Pdosql();
            $sltarr = array();

            // baord
            $sql->query(
                "
                select board.cfg_value as board_id, board_title.cfg_value as board_title
                from {$sql->table("config")} board
                left outer join {$sql->table("config")} board_title
                on board.cfg_type=board_title.cfg_type and board_title.cfg_key='title'
                where board.cfg_type like 'mod:board:config:%' and board.cfg_key='id'
                order by board.cfg_value asc;
                ", []
            );

            do {
                $arr['type'] = 'board';
                $arr['type-txt'] = '게시판';
                $arr['title'] = $sql->fetch('board_title');
                $arr['id'] = $sql->fetch('board_id');
                $arr['option-txt'] = $arr['type-txt'].'모듈 - '.$arr['title'].' ('.$arr['id'].')';

                $sltarr[] = $arr;

            } while($sql->nextRec());

            // contents
            $sql->query(
                "
                select *
                from {$sql->table("mod:contents")}
                order by data_key asc
                ", []
            );

            do {
                $arr['type'] = 'contents';
                $arr['type-txt'] = '콘텐츠';
                $arr['title'] = $sql->fetch('title');
                $arr['id'] = $sql->fetch('data_key');
                $arr['option-txt'] = $arr['type-txt'].'모듈 - '.$arr['title'].' ('.$arr['id'].')';

                $sltarr[] = $arr;

            } while($sql->nextRec());

            return $sltarr;
        }
    }

    public function make()
    {
        $req = Method::request('get', 'idx');

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("mod:search")}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );
        $arr = $sql->fetchs();

        $arr[0]['module'] = '';
        $arr[0]['limit'] = '';

        if ($arr['opt']) {
            $opt_exp = explode('|', $arr['opt']);
            $arr[0]['module'] = $opt_exp[0].'|'.$opt_exp[1];
            $arr[0]['limit'] = $opt_exp[2];
        }

        $write = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $write[$key] = $value;
            }

        } else {
            $write = null;
        }

        $this->set('write', $write);
        $this->set('get_modules', get_modules());
    }
}

//
// Controller for submit
// ( SearchModify )
//
class SearchModify_submit{

    public function init()
    {
        $manage = new ManageFunc();
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'idx, title, href, module, limit');

        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );
        Valid::get(
            array(
                'input' => 'href',
                'value' => $req['href']
            )
        );
        Valid::get(
            array(
                'input' => 'module',
                'value' => $req['module'],
                'check' => array(
                    'selected' => true
                )
            )
        );

        $sql->query(
            "
            update {$sql->table("mod:search")}
            set
            title=:col1, href=:col2, opt=:col3
            where idx=:col4
            ",
            array(
                $req['title'], $req['href'], $req['module'].'|'.$req['limit'], $req['idx']
            )
        );

        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'ph_mod_search_manage.search_result(\'secc_modify\');'
            )
        );
        Valid::turn();
    }

}
