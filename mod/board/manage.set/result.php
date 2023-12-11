<?php
use Corelib\Func;
use Corelib\Method;
use Corelib\Valid;
use Corelib\Session;
use Make\Database\Pdosql;
use Make\Library\Uploader;
use Make\Library\Paging;
use Make\Library\Mail;
use Manage\ManageFunc;
use Module\Board\Library as Board_Library;

//
// Controller for display
// https://{domain}/manage/mod/board/result/result
//
class Result extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/result.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function board_total($arr)
        {
            return Func::number($arr['board_total']);
        }

        function data_total($arr)
        {
            global $board_id;

            $sql = new Pdosql();

            $board_id = $arr['cfg_value'];

            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$board_id)}
                where use_notice='Y' or use_notice='N'
                ",
                array(
                    $board_id
                )
            );
            return Func::number($sql->getcount());
        }
    }

    public function make()
    {
        global $PARAM, $sortby, $searchby, $orderby;

        $sql = new Pdosql();
        $sql2 = new Pdosql();
        $paging = new Paging();
        $manage = new ManageFunc();

        $sql->query(
            "
            select
            ( select count(*) from {$sql->table("config")} where cfg_type like 'mod:board:config:%' and cfg_key='id' ) board_total
            ", []
        );
        $sort_arr['board_total'] = $sql->fetch('board_total');

        // orderby
        if (!$PARAM['ordtg']) $PARAM['ordtg'] = 'config.cfg_regdate';
        if (!$PARAM['ordsc']) $PARAM['ordsc'] = 'desc';
        $orderby = $PARAM['ordtg'].' '.$PARAM['ordsc'];

        // list
        $sql->query(
            $paging->query(
                "
                select config.*,board_name_tbl.cfg_value AS board_name
                from {$sql->table("config")} config
                left outer join {$sql->table("config")} board_name_tbl
                on config.cfg_type=board_name_tbl.cfg_type and board_name_tbl.cfg_key='title'
                where config.cfg_type like 'mod:board:config:%' and config.cfg_key='id' $searchby
                order by $orderby
                ", []
            )
        );
        $list_cnt = $sql->getcount();
        $total_cnt = Func::number($paging->totalCount);
        $print_arr = array();

        if ($list_cnt > 0) {
            do {
                $arr = $sql->fetchs();

                $sql2->query(
                    "
                    select *
                    from {$sql2->table("config")}
                    where cfg_type='mod:board:config:{$arr['cfg_value']}'
                    ", []
                );

                $arr2 = array();

                do {
                    $arr2[$sql2->fetch('cfg_key')] = $sql2->fetch('cfg_value');

                } while($sql2->nextRec());

                $arr['no'] = $paging->getnum();
                $arr['id'] = $arr2['id'];
                $arr['title'] = $arr2['title'];
                $arr['list_level'] = $arr2['list_level'];
                $arr['read_level'] = $arr2['read_level'];
                $arr['write_level'] = $arr2['write_level'];
                $arr['regdate'] = Func::datetime($arr['cfg_regdate']);
                $arr[0]['data_total'] = data_total($arr);

                $print_arr[] = $arr;

            } while ($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('board_total', board_total($sort_arr));
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param()));
        $this->set('print_arr', $print_arr);

    }

    public function form($idx)
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makeBoardForm'.$idx);
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_BOARD_DIR.'/result/result-clone-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Result_clone )
//
class Result_clone_submit{

    public function init()
    {
        global $board_id, $clone_id, $board_title;

        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'board_id, clone_id');

        Valid::get(
            array(
                'input' => 'board_id',
                'value' => $req['board_id'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'clone_id',
                'value' => $req['clone_id'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );

        $board_id = $req['board_id'];
        $clone_id = $req['clone_id'];

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$board_id}'
            ", []
        );

        if ($sql->getcount() < 1) Valid::error('', '복제할 게시판이 존재하지 않습니다.');

        $arr = array();

        do {
            $cfg = $sql->fetchs();
            $arr[$cfg['cfg_key']] = $cfg['cfg_value'];

        } while($sql->nextRec());

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$clone_id}'
            ", []
        );

        if ($sql->getcount() > 0) Valid::error('clone_id', '생성할 게시판 id가 이미 존재하는 id입니다.');

        foreach ($arr as $key => $value) {

            if ($key == 'title') $value = $arr['title'].'에서 복제됨';
            if ($key == 'id') $value = $clone_id;

            $sql->query(
                "
                insert into {$sql->table("config")}
                (cfg_type, cfg_key, cfg_value, cfg_regdate)
                values
                ('mod:board:config:{$clone_id}', :col1, :col2, now())
                ", array(
                    $key, $value
                )
            );
        }

        $sql->query(
            "
            create table if not exists {$sql->table("mod:board_data_")}$clone_id (
            idx int(11) not null auto_increment,
            category varchar(255) default null,
            ln int(11) default '0',
            rn int(11) default '0',
            mb_idx int(11) default '0',
            mb_id varchar(255) default null,
            writer varchar(255) default null,
            pwd text,
            email varchar(255) default null,
            article text,
            subject varchar(255) default null,
            file1 text,
            file1_cnt int(11) default '0',
            file2 text,
            file2_cnt int(11) default '0',
            use_secret char(1) default 'N',
            use_notice char(1) default 'N',
            use_html char(1) default 'Y',
            use_email char(1) default 'Y',
            view int(11) default '0',
            ip varchar(255) default null,
            regdate datetime default null,
            dregdate datetime default null,
            data_1 text,
            data_2 text,
            data_3 text,
            data_4 text,
            data_5 text,
            data_6 text,
            data_7 text,
            data_8 text,
            data_9 text,
            data_10 text,
            primary key(idx)
            )engine=InnoDB default charset=utf8;
            ", []
        );

        $sql->query(
            "
            create table if not exists {$sql->table("mod:board_cmt_")}$clone_id (
            idx int(11) not null auto_increment,
            ln int(11) default '0',
            rn int(11) default '0',
            bo_idx int(11) default null,
            mb_idx int(11) default '0',
            writer varchar(255) default null,
            parent_mb_idx int(11) default '0',
            parent_writer varchar(255) default null,
            comment text,
            ip varchar(255) default null,
            regdate datetime default null,
            cmt_1 text,
            cmt_2 text,
            cmt_3 text,
            cmt_4 text,
            cmt_5 text,
            cmt_6 text,
            cmt_7 text,
            cmt_8 text,
            cmt_9 text,
            cmt_10 text,
            primary key(idx)
            )engine=InnoDB default charset=utf8;
            ", []
        );

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '게시판이 성공적으로 복제 되었습니다.'
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/board/result/regist
//
class Regist extends \Controller\Make_Controller {

    public function init(){
        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/regist.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function board_theme(){
            $tpath = PH_THEME_PATH.'/mod-'.MOD_BOARD.'/board/';
            $topen = opendir($tpath);
            $topt = '';

            while ($dir = readdir($topen)) {
                if ($dir != '.' && $dir != '..') {
                    $topt .= '<option value="'.$dir.'">'.$dir.'</option>';
                    $bd_theme[] = $dir;
                }
            }
            return $topt;
        }
    }

    public function make()
    {
        $manage = new ManageFunc();

        Func::add_javascript(PH_PLUGIN_DIR.'/'.PH_PLUGIN_CKEDITOR.'/ckeditor.js');

        $manage->make_target('게시판 기본 설정|권한 설정|아이콘 출력 설정|여분필드');

        $this->set('manage', $manage);
        $this->set('print_target', $manage->print_target());
        $this->set('board_theme', board_theme());
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'makeBoardForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_BOARD_DIR.'/result/regist-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Regist )
//
class Regist_submit {

    public function init(){
        global $board_id;

        $sql = new Pdosql();
        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'id, title, theme, use_category, category, use_list, m_use_list, list_limit, m_list_limit, sbj_limit, m_sbj_limit, txt_limit, m_txt_limit, use_likes, use_reply, use_comment, use_secret, use_seek, ico_secret_def, use_file1, use_file2, use_mng_feed, file_limit, article_min_len, top_source, bottom_source, ctr_level, list_level, write_level, secret_level, comment_level, reply_level, delete_level, read_level, write_level, read_point, write_point, ico_file, ico_secret, ico_new, ico_new_case, ico_hot, ico_hot_case_1, ico_hot_case_2, ico_hot_case_3, conf_1, conf_2, conf_3, conf_4, conf_5, conf_6, conf_7, conf_8, conf_9, conf_10, conf_exp');

        $board_id = $req['id'];

        Valid::get(
            array(
                'input' => 'id',
                'value' => $req['id'],
                'check' => array(
                    'defined' => 'idx'
                )
            )
        );
        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );
        Valid::get(
            array(
                'input' => 'file_limit',
                'value' => $req['file_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 50
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_new_case',
                'value' => $req['ico_new_case'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_hot_case_1',
                'value' => $req['ico_hot_case_1'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_hot_case_2',
                'value' => $req['ico_hot_case_2'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );

        if ($req['use_category'] == 'Y' && !$req['category']) Valid::error('category', '카테고리 설정을 확인하세요.');
        if (!$req['list_limit']) $req['list_limit'] = 15;
        if (!$req['m_list_limit']) $req['m_list_limit'] = 10;
        if (!$req['sbj_limit']) $req['sbj_limit'] = 50;
        if (!$req['m_sbj_limit']) $req['m_sbj_limit'] = 30;
        if (!$req['txt_limit']) $req['txt_limit'] = 150;
        if (!$req['m_txt_limit']) $req['m_txt_limit'] = 100;
        if (!$req['article_min_len']) $req['article_min_len'] = 30;
        if (!$req['read_point']) $req['read_point'] = 0;
        if (!$req['write_point']) $req['write_point'] = 0;

        $conf_exp = $sql->etcfd_exp(implode('{|}', $req['conf_exp']));

        $req['use_list'] = $req['use_list'].'|'.$req['m_use_list'];
        $req['list_limit'] = $req['list_limit'].'|'.$req['m_list_limit'];
        $req['sbj_limit'] = $req['sbj_limit'].'|'.$req['m_sbj_limit'];
        $req['txt_limit'] = $req['txt_limit'].'|'.$req['m_txt_limit'];
        $req['ico_hot_case'] = $req['ico_hot_case_1'].'|'.$req['ico_hot_case_3'].'|'.$req['ico_hot_case_2'];

        if ($sql->table_exists('mod:board_data_'.$board_id) > 0) Valid::error('id', '이미 존재하는 게시판 id 입니다.');
        
        $sql->query(
            "
            create table if not exists {$sql->table("mod:board_data_")}$board_id (
            idx int(11) not null auto_increment,
            category varchar(255) default null,
            ln int(11) default '0',
            rn int(11) default '0',
            mb_idx int(11) default '0',
            mb_id varchar(255) default null,
            writer varchar(255) default null,
            pwd text,
            email varchar(255) default null,
            article text,
            subject varchar(255) default null,
            file1 text,
            file1_cnt int(11) default '0',
            file2 text,
            file2_cnt int(11) default '0',
            use_secret char(1) default 'N',
            use_notice char(1) default 'N',
            use_html char(1) default 'Y',
            use_email char(1) default 'Y',
            view int(11) default '0',
            ip varchar(255) default null,
            regdate datetime default null,
            dregdate datetime default null,
            data_1 text,
            data_2 text,
            data_3 text,
            data_4 text,
            data_5 text,
            data_6 text,
            data_7 text,
            data_8 text,
            data_9 text,
            data_10 text,
            primary key(idx)
            )engine=InnoDB default charset=utf8;
            ", []
        );

        $sql->query(
            "
            create table if not exists {$sql->table("mod:board_cmt_")}$board_id (
            idx int(11) not null auto_increment,
            ln int(11) default '0',
            rn int(11) default '0',
            bo_idx int(11) default null,
            mb_idx int(11) default '0',
            writer varchar(255) default null,
            parent_mb_idx int(11) default '0',
            parent_writer varchar(255) default null,
            comment text,
            ip varchar(255) default null,
            regdate datetime default null,
            cmt_1 text,
            cmt_2 text,
            cmt_3 text,
            cmt_4 text,
            cmt_5 text,
            cmt_6 text,
            cmt_7 text,
            cmt_8 text,
            cmt_9 text,
            cmt_10 text,
            primary key(idx)
            )engine=InnoDB default charset=utf8;
            ", []
        );

        $data = array(
            'id' => $req['id'],
            'theme' => $req['theme'],
            'title' => $req['title'],
            'use_list' => $req['use_list'],
            'use_secret' => $req['use_secret'],
            'use_seek' => $req['use_seek'],
            'use_comment' => $req['use_comment'],
            'use_likes' => $req['use_likes'],
            'use_reply' => $req['use_reply'],
            'use_file1' => $req['use_file1'],
            'use_file2' => $req['use_file2'],
            'use_mng_feed' => $req['use_mng_feed'],
            'use_category' => $req['use_category'],
            'category' => $req['category'],
            'file_limit' => $req['file_limit'],
            'list_limit' => $req['list_limit'],
            'sbj_limit' => $req['sbj_limit'],
            'txt_limit' => $req['txt_limit'],
            'article_min_len' => $req['article_min_len'],
            'list_level' => $req['list_level'],
            'write_level' => $req['write_level'],
            'secret_level' => $req['secret_level'],
            'comment_level' => $req['comment_level'],
            'delete_level' => $req['delete_level'],
            'read_level' => $req['read_level'],
            'ctr_level' => $req['ctr_level'],
            'reply_level' => $req['reply_level'],
            'write_point' => $req['write_point'],
            'read_point' => $req['read_point'],
            'top_source' => $req['top_source'],
            'bottom_source' => $req['bottom_source'],
            'ico_file' => $req['ico_file'],
            'ico_secret' => $req['ico_secret'],
            'ico_secret_def' => $req['ico_secret_def'],
            'ico_new' => $req['ico_new'],
            'ico_new_case' => $req['ico_new_case'],
            'ico_hot' => $req['ico_hot'],
            'ico_hot_case' => $req['ico_hot_case'],
            'conf_1' => $req['conf_1'],
            'conf_2' => $req['conf_2'],
            'conf_3' => $req['conf_3'],
            'conf_4' => $req['conf_4'],
            'conf_5' => $req['conf_5'],
            'conf_6' => $req['conf_6'],
            'conf_7' => $req['conf_7'],
            'conf_8' => $req['conf_8'],
            'conf_9' => $req['conf_9'],
            'conf_10' => $req['conf_10'],
            'conf_exp' => $conf_exp
        );

        $insert_qry = array();
        foreach ($data as $key => $value) {
            $insert_qry[] = "('mod:board:config:{$req['id']}', '".addslashes($key)."', '".addslashes($value)."', now())";
        }
        $insert_qry = implode(',', $insert_qry);

        $sql->query(
            "
            insert into {$sql->table("config")}
            (cfg_type, cfg_key, cfg_value, cfg_regdate)
            values
            {$insert_qry}
            ", []
        );

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 추가 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mod/'.MOD_BOARD.'/result/modify?id='.$req['id']
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/board/result/modify
//
class Modify extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/modify.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function board_theme($arr)
        {
            $tpath = PH_THEME_PATH.'/mod-'.MOD_BOARD.'/board/';
            $topen = opendir($tpath);
            $topt = '';

            while ($dir = readdir($topen)) {
                $slted = '';

                if ($dir != '.' && $dir != '..') {
                    if ($dir == $arr['theme']) $slted = 'selected';
                    $topt .= '<option value="'.$dir.'" '.$slted.'>'.$dir.'</option>';
                }
            }
            return $topt;
        }

        function set_chked($arr, $val)
        {
            $setarr = array(
                'Y' => '',
                'N' => '',
                'AND' => '',
                'OR' => ''
            );
            foreach ($setarr as $key => $value) {
                if ($key == $arr[$val]) $setarr[$key] = 'checked';
            }

            return $setarr;
        }
    }

    public function make()
    {
        $sql = new Pdosql();
        $manage = new ManageFunc();

        $req = Method::request('get', 'id');

        Func::add_javascript(PH_PLUGIN_DIR.'/'.PH_PLUGIN_CKEDITOR.'/ckeditor.js');

        $manage->make_target('게시판 기본 설정|권한 설정|아이콘 출력 설정|여분필드');

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$req['id']}'
            ", []
        );

        if ($sql->getcount() < 1) Func::err_back('게시판이 존재하지 않습니다.');

        $arr = array();

        do {
            $sql->specialchars = 1;
            $sql->nl2br = 1;

            $cfg = $sql->fetchs();
            $arr[$cfg['cfg_key']] = $cfg['cfg_value'];

            if ($cfg['cfg_key'] == 'top_source' || $cfg['cfg_key'] == 'bottom_source') {
                $sql->specialchars = 0;
                $sql->nl2br = 0;

                $arr[$cfg['cfg_key']] = $sql->fetch('cfg_value');
            }

        } while($sql->nextRec());

        $use_list = explode('|', $arr['use_list']);
        $arr['use_list'] = $use_list[0];
        $arr['m_use_list'] = $use_list[1];

        $list_limit = explode('|', $arr['list_limit']);
        $arr['list_limit'] = $list_limit[0];
        $arr['m_list_limit'] = $list_limit[1];

        $sbj_limit = explode('|', $arr['sbj_limit']);
        $arr['sbj_limit'] = $sbj_limit[0];
        $arr['m_sbj_limit'] = $sbj_limit[1];

        $txt_limit = explode('|', $arr['txt_limit']);
        $arr['txt_limit'] = $txt_limit[0];
        $arr['m_txt_limit'] = $txt_limit[1];

        $ico_hot_case = explode('|', $arr['ico_hot_case']);
        $arr['ico_hot_case_1'] = $ico_hot_case[0];
        $arr['ico_hot_case_2'] = $ico_hot_case[2];
        $arr['ico_hot_case_3'] = $ico_hot_case[1];

        $ex = explode('|', $arr['conf_exp']);

        for ($i = 1; $i <= 10; $i++) {
            $arr['conf_'.$i.'_exp'] = $ex[$i - 1];
        }

        $write = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $write[$key] = $value;
            }

        } else {
            $write = null;
        }

        $this->set('manage', $manage);
        $this->set('write', $write);
        $this->set('print_target', $manage->print_target());
        $this->set('board_theme', board_theme($arr));
        $this->set('use_category', set_chked($arr, 'use_category'));
        $this->set('use_list', set_chked($arr, 'use_list'));
        $this->set('m_use_list', set_chked($arr, 'm_use_list'));
        $this->set('use_likes', set_chked($arr, 'use_likes'));
        $this->set('use_reply', set_chked($arr, 'use_reply'));
        $this->set('use_comment', set_chked($arr, 'use_comment'));
        $this->set('use_secret', set_chked($arr, 'use_secret'));
        $this->set('ico_secret_def', set_chked($arr, 'ico_secret_def'));
        $this->set('use_seek', set_chked($arr, 'use_seek'));
        $this->set('use_file1', set_chked($arr, 'use_file1'));
        $this->set('use_file2', set_chked($arr, 'use_file2'));
        $this->set('use_mng_feed', set_chked($arr, 'use_mng_feed'));
        $this->set('ico_file', set_chked($arr, 'ico_file'));
        $this->set('ico_secret', set_chked($arr, 'ico_secret'));
        $this->set('ico_new', set_chked($arr, 'ico_new'));
        $this->set('ico_hot', set_chked($arr, 'ico_hot'));
        $this->set('ico_hot_case_3', set_chked($arr, 'ico_hot_case_3'));
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'modifyBoardForm');
        $form->set('type', 'html');
        $form->set('action', PH_MANAGE_DIR.'/mod/'.MOD_BOARD_DIR.'/result/modify-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Modify )
//
class Modify_submit {

    public function init()
    {
        global $req;

        $manage = new ManageFunc();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'mode, id, title, theme, use_category, category, use_list, m_use_list, list_limit, m_list_limit, sbj_limit, m_sbj_limit, txt_limit, m_txt_limit, use_likes, use_reply, use_comment, use_secret, ico_secret_def, use_seek, use_file1, use_file2, use_mng_feed, file_limit, article_min_len, top_source, bottom_source, ctr_level, list_level, write_level, secret_level, comment_level, reply_level, delete_level, read_level, write_level, read_point, write_point, ico_file, ico_secret, ico_new, ico_new_case, ico_hot, ico_hot_case_1, ico_hot_case_2, ico_hot_case_3, conf_1, conf_2, conf_3, conf_4, conf_5, conf_6, conf_7, conf_8, conf_9, conf_10, conf_exp');
        $manage->req_hidden_inp('post');

        switch ($req['mode']) {
            case 'mod' :
                $this->get_modify();
                break;

            case 'del' :
                $this->get_delete();
                break;
        }
    }

    //
    // modify
    //
    public function get_modify()
    {
        global $req;

        $sql = new Pdosql();

        Valid::get(
            array(
                'input' => 'title',
                'value' => $req['title']
            )
        );
        Valid::get(
            array(
                'input' => 'list_limit',
                'value' => $req['list_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'm_list_limit',
                'value' => $req['m_list_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'sbj_limit',
                'value' => $req['sbj_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'm_sbj_limit',
                'value' => $req['m_sbj_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'txt_limit',
                'value' => $req['txt_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'm_txt_limit',
                'value' => $req['m_txt_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'file_limit',
                'value' => $req['file_limit'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 50
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_new_case',
                'value' => $req['ico_new_case'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_hot_case_1',
                'value' => $req['ico_hot_case_1'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );
        Valid::get(
            array(
                'input' => 'ico_hot_case_2',
                'value' => $req['ico_hot_case_2'],
                'check' => array(
                    'charset' => 'number',
                    'maxlen' => 10
                )
            )
        );

        if ($req['use_category'] == 'Y' && !$req['category']) Valid::error('category', '카테고리 설정을 확인하세요.');
        if (!$req['list_limit']) $req['list_limit'] = 15;
        if (!$req['m_list_limit']) $req['m_list_limit'] = 10;
        if (!$req['sbj_limit']) $req['sbj_limit'] = 50;
        if (!$req['m_sbj_limit']) $req['m_sbj_limit'] = 30;
        if (!$req['txt_limit']) $req['txt_limit'] = 150;
        if (!$req['m_txt_limit']) $req['m_txt_limit'] = 100;
        if (!$req['article_min_len'] || $req['article_min_len'] < 0) $req['article_min_len'] = 0;
        if (!$req['read_point']) $req['read_point'] = 0;
        if (!$req['write_point']) $req['write_point'] = 0;

        $conf_exp = $sql->etcfd_exp(implode('{|}', $req['conf_exp']));

        $req['use_list'] = $req['use_list'].'|'.$req['m_use_list'];
        $req['list_limit'] = $req['list_limit'].'|'.$req['m_list_limit'];
        $req['sbj_limit'] = $req['sbj_limit'].'|'.$req['m_sbj_limit'];
        $req['txt_limit'] = $req['txt_limit'].'|'.$req['m_txt_limit'];
        $req['ico_hot_case'] = $req['ico_hot_case_1'].'|'.$req['ico_hot_case_3'].'|'.$req['ico_hot_case_2'];

        $data = array(
            'theme' => $req['theme'],
            'title' => $req['title'],
            'use_list' => $req['use_list'],
            'use_secret' => $req['use_secret'],
            'use_seek' => $req['use_seek'],
            'use_comment' => $req['use_comment'],
            'use_likes' => $req['use_likes'],
            'use_reply' => $req['use_reply'],
            'use_file1' => $req['use_file1'],
            'use_file2' => $req['use_file2'],
            'use_mng_feed' => $req['use_mng_feed'],
            'use_category' => $req['use_category'],
            'category' => $req['category'],
            'file_limit' => $req['file_limit'],
            'list_limit' => $req['list_limit'],
            'sbj_limit' => $req['sbj_limit'],
            'txt_limit' => $req['txt_limit'],
            'article_min_len' => $req['article_min_len'],
            'list_level' => $req['list_level'],
            'write_level' => $req['write_level'],
            'secret_level' => $req['secret_level'],
            'comment_level' => $req['comment_level'],
            'delete_level' => $req['delete_level'],
            'read_level' => $req['read_level'],
            'ctr_level' => $req['ctr_level'],
            'reply_level' => $req['reply_level'],
            'write_point' => $req['write_point'],
            'read_point' => $req['read_point'],
            'top_source' => $req['top_source'],
            'bottom_source' => $req['bottom_source'],
            'ico_file' => $req['ico_file'],
            'ico_secret' => $req['ico_secret'],
            'ico_secret_def' => $req['ico_secret_def'],
            'ico_new' => $req['ico_new'],
            'ico_new_case' => $req['ico_new_case'],
            'ico_hot' => $req['ico_hot'],
            'ico_hot_case' => $req['ico_hot_case'],
            'conf_1' => $req['conf_1'],
            'conf_2' => $req['conf_2'],
            'conf_3' => $req['conf_3'],
            'conf_4' => $req['conf_4'],
            'conf_5' => $req['conf_5'],
            'conf_6' => $req['conf_6'],
            'conf_7' => $req['conf_7'],
            'conf_8' => $req['conf_8'],
            'conf_9' => $req['conf_9'],
            'conf_10' => $req['conf_10'],
            'conf_exp' => $conf_exp
        );

        foreach ($data as $key => $value) {
            $sql->query(
                "
                select *
                from {$sql->table("config")}
                where cfg_type='mod:board:config:{$req['id']}' and cfg_key=:col1
                ",
                array(
                    $key
                )
            );
            if ($sql->getcount() < 1) {
                $sql->query(
                    "
                    insert into
                    {$sql->table("config")}
                    (cfg_type, cfg_key)
                    values
                    ('mod:board:config:{$req['id']}', :col1)
                    ",
                    array(
                        $key
                    )
                );
            }
            $sql->query(
                "
                update
                {$sql->table("config")}
                set
                cfg_value=:col1
                where cfg_type='mod:board:config:{$req['id']}' and cfg_key=:col2
                ",
                array(
                    $value, $key
                )
            );
        }

        Valid::set(
            array(
                'return' => 'alert->reload',
                'msg' => '성공적으로 변경 되었습니다.'
            )
        );
        Valid::turn();
    }

    //
    // delete
    //
    public function get_delete()
    {
        global $board_id, $req;

        $sql = new Pdosql();
        $uploader = new Uploader();
        $manage = new ManageFunc();

        $sql->query(
            "
            select *
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$req['id']}' and cfg_key='id' and cfg_value='{$req['id']}'
            ", []
        );

        $board_id = $sql->fetch('cfg_value');

        if ($sql->getcount() < 1) Valid::error('', '게시판이 존재하지 않습니다.');

        $sql->query(
            "
            delete
            from {$sql->table("config")}
            where cfg_type='mod:board:config:{$board_id}'
            ", []
        );

        $sql->query(
            "
            drop table {$sql->table("mod:board_data_")}$board_id
            ", []
        );

        $sql->query(
            "
            drop table {$sql->table("mod:board_cmt_")}$board_id
            ", []
        );

        $uploader->path = MOD_BOARD_DATA_PATH.'/'.$board_id.'/thumb';
        $uploader->dropdir();
        $uploader->path = MOD_BOARD_DATA_PATH.'/'.$board_id;
        $uploader->dropdir();

        Valid::set(
            array(
                'return' => 'alert->location',
                'msg' => '성공적으로 삭제 되었습니다.',
                'location' => PH_MANAGE_DIR.'/mod/'.MOD_BOARD.'/result/result'.$manage->retlink('')
            )
        );
        Valid::turn();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/board/result/board
//
class Board extends \Controller\Make_Controller {

    public function init()
    {
        global $boardconf, $req;

        $req = Method::request('get', 'id, read, category, page');

        $boardlib = new Board_Library();
        $boardconf = $boardlib->load_conf($req['id']);

        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/board.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        function data_total()
        {
            global $req;

            $sql = new Pdosql();

            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$req['id'])}
                where use_notice='Y' or use_notice='N'
                ", []
            );
            return Func::number($sql->getcount());
        }

        // 전체 게시글 개수
        function total_cnt($notice_cnt, $total_cnt)
        {
            return Func::number($notice_cnt + $total_cnt);
        }

        // 제목
        function print_subject($arr)
        {
            global $boardconf;

            return (!$arr['dregdate']) ? reply_ico($arr).Func::strcut($arr['subject'],0,$boardconf['sbj_limit']) : reply_ico($arr).'<strike>'.$arr['dregdate'].'에 삭제된 게시글입니다.'.'</strike>';
        }

        // 링크
        function get_link($arr, $thisuri, $board_id, $category)
        {
            global $manage;

            return './board-view'.$manage->lnk_def_param('&id='.$board_id.'&read='.$arr['idx'].'&category='.$category);
        }

        // 내용
        function print_article($arr)
        {
            global $boardconf;

            return Func::strcut(strip_tags(Func::htmldecode($arr['article'])), 0, $boardconf['txt_limit']);
        }

        // 첨부파일 아이콘
        function file_ico($arr)
        {
            global $boardconf;

            $is_img = false;
            $is_file = false;

            if ($boardconf['ico_file'] == 'Y') {
                for ($i = 1; $i<=2; $i++) {
                    $file_type = Func::get_filetype($arr['file'.$i]);

                    if (Func::chkintd('match', $file_type, SET_IMGTYPE)) {
                        $is_img = true;

                    } else if ($arr['file'.$i]) {
                        $is_file = true;
                    }
                }
            }

            if ($is_img === true) {
                return '<img src="'.MOD_BOARD_DIR.'/manage.set/images/picture-ico.png" align="absmiddle" title="이미지파일" alt="이미지파일" />';

            } else if ($is_file === true) {
                return '<img src="'.MOD_BOARD_DIR.'/manage.set/images/file-ico.png" align="absmiddle" title="파일" alt="파일" />';
            }
        }

        // 답글 아이콘
        function reply_ico($arr)
        {
            $nbsp = '';
            if ($arr['rn'] > 0) {
                for ($i = 1; $i <= $arr['rn']; $i++) {
                    $nbsp .= '&nbsp;&nbsp;';
                }

                return $nbsp.'<img src="'.MOD_BOARD_DIR.'/manage.set/images/reply-ico.png" align="absmiddle" title="답글" alt="답글" class="reply-ico" />&nbsp;';
            }
        }

        // 비밀글 아이콘
        function secret_ico($arr)
        {
            global $boardconf;

            return ($arr['use_secret'] == 'Y' && $boardconf['ico_secret'] == 'Y') ? '<img src="'.MOD_BOARD_DIR.'/manage.set/images/secret-ico.png" align="absmiddle" title="비밀글" alt="비밀글" />' : '';
        }

        // new 아이콘
        function new_ico($arr)
        {
            global $boardconf;

            $now_date = date('Y-m-d H:i:s');
            $wr_date = date('Y-m-d H:i:s', strtotime($arr['regdate']));

            if ( ((strtotime($now_date) - strtotime($wr_date)) / 60) < $boardconf['ico_new_case'] && $boardconf['ico_new'] == 'Y') {
                return '<img src="'.MOD_BOARD_DIR.'/manage.set/images/new-ico.png" align="absmiddle" title="NEW" alt="NEW" />';
            }
        }

        // hot 아이콘
        function hot_ico($arr)
        {
            global $boardconf;

            $ico_hot_case = explode('|', $boardconf['ico_hot_case']);

            if ($boardconf['ico_hot'] == 'Y') {
                if (($ico_hot_case[1] == 'AND' && $arr['likes_cnt'] >= $ico_hot_case[0] && $arr['view'] >= $ico_hot_case[2]) || ($ico_hot_case[1] == 'OR' && ($arr['likes_cnt'] >= $ico_hot_case[0] || $arr['view'] >= $ico_hot_case[2]))) {
                    return '<img src="'.MOD_BOARD_DIR.'/manage.set/images/hot-ico.png" align="absmiddle" title="HOT" alt="HOT" />';
                }
            }
        }

        // 댓글 개수
        function comment_cnt($arr)
        {
            global $boardconf;

            return ($arr['comment_cnt'] > 0 && $boardconf['use_comment'] == 'Y') ? Func::number($arr['comment_cnt']) : '';
        }

        // 작성 버튼
        function write_btn($page, $category, $thisuri)
        {
            global $manage, $MB, $boardconf;

            return ($MB['level'] <= $boardconf['write_level']) ? '<a href="write'.$manage->lnk_def_param('&id='.$boardconf['id'].'&category='.urlencode($category).'&wrmode=').'" class="btn1">글 작성</a>' : '';
        }

        // 게시물 번호
        function print_number($arr, $read, $paging)
        {
            return $paging->getnum();
        }

        // 회원 이름
        function print_writer($arr)
        {
            return ($arr['mb_idx'] != 0) ? '<a href="'.PH_MANAGE_DIR.'/member/modify?idx='.$arr['mb_idx'].'" target="_blank">'.$arr['writer'].'</a>' : $arr['writer'];
        }

        // 카테고리
        function category_sort($category, $where, $keyword, $thisuri)
        {
            global $boardconf, $req;

            if (!$boardconf['category']) return;

            $cat_exp = explode('|', $boardconf['category']);
            $html = '';

            if ($boardconf['use_category'] != 'Y') return;

            for ($i = 0; $i < count($cat_exp); $i++) {
                $html .= '<li><a href="board?id='.$req['id'].'&category='.urlencode($cat_exp[$i]).'"><em>'.$cat_exp[$i].'</em></a></li>'.PHP_EOL;
            }

            return $html;
        }

        // where selectbox 선택 처리
        function where_slted($where)
        {
            $arr = array('all', 'subjectAndArticle', 'subject', 'article', 'writer', 'mb_id');
            $opt = array();

            foreach ($arr as $key => $value) {
                if ($where == $value) {
                    $opt[$value] = 'selected';

                } else {
                    $opt[$value] = '';
                }
            }

            return $opt;
        }

        // list arr setting
        function get_listarr($req, $arr, $paging, $thisuri, $keyword, $category)
        {
            global $PARAM;

            $arr['view'] = Func::number($arr['view']);
            $arr['date'] = Func::date($arr['regdate']);
            $arr['datetime'] = Func::datetime($arr['regdate']);
            $arr[0]['number'] = print_number($arr, $req['read'],$paging);
            $arr[0]['get_link'] = get_link($arr, $thisuri, $req['id'], $category);
            $arr[0]['secret_ico'] = secret_ico($arr);
            $arr[0]['file_ico'] = file_ico($arr);
            $arr[0]['new_ico'] = new_ico($arr);
            $arr[0]['hot_ico'] = hot_ico($arr);
            $arr[0]['subject'] = print_subject($arr);
            $arr[0]['article'] = print_article($arr);
            $arr[0]['comment_cnt'] = comment_cnt($arr);
            $arr[0]['writer'] = print_writer($arr);

            return $arr;
        }
    }

    public function make()
    {
        global $PARAM, $manage, $searchby, $req, $boardconf;

        $sql = new Pdosql();
        $paging = new Paging();
        $manage = new ManageFunc();

        $board_id = $req['id'];

        // board_id 검사
        if (!$sql->table_exists("mod:board_data_".$req['id'])) Func::err_back('게시판이 존재하지 않습니다.');
        $thisuri = Func::thisuri();

        // 카테고리 처리
        $category = (!empty($req['category'])) ? urldecode($req['category']) : '';
        $search = '';

        if ($category) $search = 'and board.category=\''.addslashes($req['category']).'\'';

        // 검색 키워드 처리
        $keyword = (!empty($PARAM['keyword'])) ? htmlspecialchars(urlencode($PARAM['keyword'])) : '';

        if ($keyword) {
            $keyword = urldecode($PARAM['keyword']);
            $where_arr = array('subject', 'article', 'writer', 'mb_id');

            switch ($PARAM['where']) {
                case 'subjectAndArticle' :
                    $search .= 'and (';
                    $search .= 'board.subject like \'%'.addslashes($PARAM['keyword']).'%\'';
                    $search .= 'or board.article like \'%'.addslashes($PARAM['keyword']).'%\'';
                    $search .= ')';
                    break;

                case 'subject' :
                case 'article' :
                case 'writer' :
                case 'mb_id' :
                    $search .= 'and board.'.addslashes($PARAM['where']).' like \'%'.addslashes($PARAM['keyword']).'%\'';
                    break;

                default :
                    $search .= 'and (';
                    foreach ($where_arr as $key => $value) {
                        $search .= ($key > 0 ? ' or ' : '').'board.'.$value.' like \'%'.addslashes($PARAM['keyword']).'%\'';
                    }
                    $search .= ')';
            }
        }

        $is_category_show = ($boardconf['use_category'] == 'Y' && $boardconf['category'] != '') ? true : false;
        $is_comment_show = ($boardconf['use_comment'] == 'Y') ? true : false;
        $is_likes_show = ($boardconf['use_likes'] == 'Y') ? true : false;

        // notice
        $sql->query(
            "
            select *,
            ( select count(*) from {$sql->table("mod:board_cmt_".$board_id)} where bo_idx=board.idx ) comment_cnt,
            ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=board.idx and likes>0 ) likes_cnt,
            ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=board.idx and unlikes>0 ) unlikes_cnt
            from {$sql->table("mod:board_data_".$board_id)} board
            left outer join {$sql->table("member")} member
            on board.mb_idx=member.mb_idx
            where board.use_notice='Y'
            order by board.idx desc
            ", []
        );
        $notice_cnt = $sql->getcount();
        $print_notice = array();

        if ($notice_cnt > 0) {
            do {
                $arr = $sql->fetchs();
                $print_notice[] = get_listarr($req, $arr, $paging, $thisuri, $keyword, $category);

            } while ($sql->nextRec());
        }

        // list
        $paging->thispage = $thisuri;
        $paging->setlimit($boardconf['list_limit']);

        $sql->query(
            $paging->query(
                "
                select *,
                ( select count(*) from {$sql->table("mod:board_cmt_".$board_id)} where bo_idx=board.idx ) comment_cnt,
                ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=board.idx and likes>0 ) likes_cnt,
                ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=board.idx and unlikes>0 ) unlikes_cnt
                from {$sql->table("mod:board_data_".$board_id)} board
                left outer join {$sql->table("member")} member
                on board.mb_idx=member.mb_idx
                where board.use_notice='N' $search
                order by board.ln desc, board.rn asc, board.regdate desc
                ", []
            )
        );
        $total_cnt = Func::number($paging->totalCount);
        $print_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $arr = $sql->fetchs();
                $print_arr[] = get_listarr($req, $arr, $paging, $thisuri, $keyword, $category);

            } while($sql->nextRec());
        }

        $this->set('manage', $manage);
        $this->set('write_btn', write_btn($req['page'], $category, $thisuri));
        $this->set('category', $category);
        $this->set('board_id', $req['id']);
        $this->set('where', $PARAM['where']);
        $this->set('keyword', $PARAM['keyword']);
        $this->set('board_id', $req['id']);
        $this->set('data_total', data_total());
        $this->set('category_sort', category_sort($category, $PARAM['where'], $keyword, $thisuri));
        $this->set('is_category_show', $is_category_show);
        $this->set('is_comment_show', $is_comment_show);
        $this->set('is_likes_show', $is_likes_show);
        $this->set('pagingprint', $paging->pagingprint($manage->pag_def_param().'&id='.$req['id'].'&category='.$category));
        $this->set('print_notice', $print_notice);
        $this->set('print_arr', $print_arr);

    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board-listForm');
        $form->set('type', 'static');
        $form->set('target', 'view');
        $form->set('method', 'get');
        $form->run();
    }

    public function sch_form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board-sch');
        $form->set('type', 'static');
        $form->set('target', 'view');
        $form->set('method', 'get');
        $form->run();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/board/result/write
//
class Write extends \Controller\Make_Controller {

    static public $boardconf;

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/write.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        // category
        function category_option($arr, $category)
        {
            $cat = explode('|', Write::$boardconf['category']);
            $opt = '';

            for ($i = 0; $i < count($cat); $i++) {
                $slted = '';

                if (isset($arr['category']) && urldecode($cat[$i]) == $arr['category']) $slted = 'selected';
                if (urldecode($cat[$i]) == $category) $slted = 'selected';

                $opt .= '<option value="'.$cat[$i].'" '.$slted.'>'.$cat[$i].'</option>';
            }

            return $opt;
        }

        // 파일명
        function uploaded_file($arr, $wrmode)
        {
            if ($wrmode != 'reply') {
                $files = array();

                for ($i = 1; $i <= 2; $i++) {
                    $files[$i] = '';

                    if (!empty($arr['file'.$i])) {
                        $fileinfo = Func::get_fileinfo($arr['file'.$i]);
                        $files[$i] = $fileinfo['orgfile'];
                    }

                }

                return $files;
            }
        }

        // 공지글 옵션
        function opt_notice($arr, $wrmode)
        {
            global $MB;

            $notice_opt = '<label><input type="checkbox" name="use_notice" id="use_notice" value="checked" /> 공지글 작성</label>';

            if ($MB['level'] == 1 || $MB['level'] <= Write::$boardconf['ctr_level']) {
                if (isset($arr['use_notice']) && $arr['use_notice'] == 'Y') {
                    $notice_opt = '<label><input type="checkbox" name="use_notice" id="use_notice" value="checked" checked="checked" /> 공지글 작성</label>';

                } else if ((isset($arr['rn']) && $arr['rn'] > 0) || $wrmode == 'reply') {
                    $notice_opt =  '';
                }

            } else {
                $notice_opt =  '';
            }

            return $notice_opt;
        }

        // 비밀글 옵션
        function opt_secret($arr)
        {
            $secret_opt = '';

            if (Write::$boardconf['use_secret'] == 'Y' && ( ( isset($arr['use_secret']) && $arr['use_secret']=='Y' ) || Write::$boardconf['ico_secret_def'] == 'Y') ) {
                $secret_opt = '<label><input type="checkbox" name="use_secret" id="use_secret" value="checked" checked="checked" /> 비밀글 작성</label>';

            } else if (Write::$boardconf['use_secret'] == 'Y') {
                $secret_opt = '<label><input type="checkbox" name="use_secret" id="use_secret" value="checked" /> 비밀글 작성</label>';

            } else {
                $secret_opt = '';
            }

            return $secret_opt;
        }

        // 이메일 답변 옵션
        function opt_return_email($arr)
        {
            $email_opt = '';
            if (isset($arr['use_email']) && $arr['use_email'] == 'Y') {
                $email_opt = '<label><input type="checkbox" name="use_email" id="use_email" value="checked" checked="checked" /> 이메일로 답글 알림 수신</label>';

            } else {
                $email_opt = '<label><input type="checkbox" name="use_email" id="use_email" value="checked" /> 이메일로 답글 알림 수신</label>';
            }

            return $email_opt;
        }

        // 취소 버튼
        function cancel_btn($page, $category)
        {
            global $manage, $board_id;

            return '<a href="board'.$manage->lnk_def_param('&id='.$board_id.'&category='.$category).'" class="btn2">취소</a>';
        }

        // 글쓰기 타이틀
        function write_title($wrmode)
        {
            switch ($wrmode) {
                case 'modify' :
                    return '글 수정';
                    break;

                case 'reply' :
                    return '답글 작성';
                    break;

                default :
                    return '새로운 글 작성';
            }
        }

        // 첨부 가능한 파일 사이즈
        function print_filesize()
        {
            global $func;

            return Func::getbyte(Write::$boardconf['file_limit'], 'm').'M';
        }
    }

    public function make()
    {
        global $manage, $MB, $board_id;

        $manage = new ManageFunc();
        $sql = new Pdosql();
        $boardlib = new Board_Library();

        $req = Method::request('get', 'id, mode, wrmode, read, page, where, keyword, category');

        $board_id = $req['id'];

        // load config
        Write::$boardconf = $boardlib->load_conf($board_id);

        // 수정 or 답글인 경우 원본 글 불러옴
        if ($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') {
            $sql->query(
                "
                select board.*,ceil(board.ln) ceil_ln,
                ( select count(*) from {$sql->table("mod:board_data_".$board_id)} where ln<=((ceil_ln/1000)*1000) and ln>((ceil_ln/1000)*1000)-1000 and rn>0 ) reply_cnt
                from {$sql->table("mod:board_data_".$board_id)} board
                where board.idx=:col1
                ",
                array(
                    $req['read']
                )
            );
            $arr = $sql->fetchs();
            $sql->specialchars = 1;
            $sql->nl2br = 0;

            if ($sql->getcount() < 1) Func::err_back('해당 글이 존재하지 않습니다.');

            $arr['article'] = $sql->fetch('article');
            $arr['wdate_date'] = substr($arr['regdate'], 0, 10);
            $arr['wdate_h'] = substr($arr['regdate'], 11, 2);
            $arr['wdate_i'] = substr($arr['regdate'], 14, 2);
            $arr['wdate_s'] = substr($arr['regdate'], 17, 2);

            if ($req['wrmode'] == 'reply') {
                if ($arr['use_html'] == 'Y') {
                    $arr['article'] = '<br /><br /><br /><div><strong>Org: '.$arr['subject'].'</strong><br />'.$arr['article'].'</div>';

                } else {
                    $arr['article'] = '\n\n\nOrg: '.$arr['subject'].'\n'.$arr['article'];
                }

                $arr['subject'] = 'Re: '.$arr['subject'];
            }

        } else {
            $arr = null;
        }

        // check
        if (!$board_id) Func::err_back('게시판이 지정되지 않았습니다.');

        if (!$req['wrmode'] || $req['wrmode'] == 'reply' && Write::$boardconf['write_point'] < 0) {
            if ($MB['point'] < (0 - Write::$boardconf['write_point'])) Func::err_back('포인트가 부족하여 글을 작성할 수 없습니다.');
        }
        if ($req['wrmode'] == 'reply' && Write::$boardconf['use_reply'] == 'N') Func::err_back('답변글을 등록할 수 없습니다.');

        // 삭제된 게시글인지 검사
        if (($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') && $arr['dregdate']) Func::err_back('삭제된 게시물입니다.');

        // 답글 모드인 경우 권한 검사
        if ($req['wrmode'] == 'reply' && $arr['use_notice'] == 'Y') Func::err_back('공지글에는 답글을 달 수 없습니다.');

        //작성 폼 노출
        if ($req['wrmode'] == 'modify' && $arr['mb_idx'] == '0') {
            $is_writer_show = true;
            $is_pwd_show = true;
            $is_email_show = true;

        } else {
            $is_writer_show = false;
            $is_pwd_show = false;
            $is_email_show = false;
        }

        $is_file_show = array();

        for ($i = 1; $i <= 2; $i++) {
            $is_file_show[$i] = (Write::$boardconf['use_file'.$i] == 'Y') ? true : false;

            $is_filename_show[$i] = false;

            if ($req['wrmode'] == 'modify') {
                if ($arr['file'.$i] != '') $is_filename_show[$i] = true;

            } else {
                $is_filename_show[$i] = false;
            }
        }

        $is_category_show = (Write::$boardconf['use_category'] == 'Y' && Write::$boardconf['category'] != '' && $req['wrmode'] != 'reply' && $arr['rn'] == 0 && $arr['reply_cnt'] < 1) ? true : false;

        $write = array();
        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $write[$key] = $value;
            }

        } else {
            $write = array('subject' => '', 'article' => '', 'writer' => '', 'pwd' => '', 'email' => '', 'wdate_date' => '', 'wdate_h' => '', 'wdate_i' => '', 'wdate_s' => '');

            for ($i = 1; $i <= 10; $i++) {
                $write['data_'.$i] = '';
            }
        }

        if ($req['wrmode'] != 'modify') {
            $write['wdate_date'] = '';
            $write['wdate_h'] = '';
            $write['wdate_i'] = '';
            $write['wdate_s'] = '';
        }

        $this->set('manage', $manage);
        $this->set('write', $write);
        $this->set('uploaded_file', uploaded_file($arr,$req['wrmode']));
        $this->set('cancel_btn', cancel_btn($req['page'], $req['category']));
        $this->set('is_category_show', $is_category_show);
        $this->set('is_writer_show', $is_writer_show);
        $this->set('is_pwd_show', $is_pwd_show);
        $this->set('is_email_show', $is_email_show);
        $this->set('is_file_show', $is_file_show);
        $this->set('is_filename_show', $is_filename_show);
        $this->set('board_id', $board_id);
        $this->set('mode', $req['mode']);
        $this->set('wrmode', $req['wrmode']);
        $this->set('read', $req['read']);
        $this->set('page', $req['page']);
        $this->set('where', $req['where']);
        $this->set('keyword', $req['keyword']);
        $this->set('category', $req['category']);
        $this->set('thisuri', Func::thisuri());
        $this->set('write_title', write_title($req['wrmode']));
        $this->set('category_option', category_option($arr, $req['category']));
        $this->set('opt_notice', opt_notice($arr,$req['wrmode']));
        $this->set('opt_secret', opt_secret($arr));
        $this->set('opt_return_email', opt_return_email($arr));
        $this->set('print_filesize', print_filesize());
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board-writeForm');
        $form->set('type', 'multipart');
        $form->set('action', MOD_BOARD_DIR.'/controller/write/write-submit');
        $form->run();
    }

}

//
// Controller for display
// https://{domain}/manage/mod/board/result/board-view
//
class Board_view extends \Controller\Make_Controller {

    static public $boardconf;

    public function init()
    {
        $this->layout()->mng_head();
        $this->layout()->view(MOD_BOARD_PATH.'/manage.set/html/board-view.tpl.php');
        $this->layout()->mng_foot();
    }

    public function func()
    {
        // 비밀글 아이콘 출력
        function secret_ico($arr)
        {
            return ($arr['use_secret'] == 'Y') ? '<img src=\''.MOD_BOARD_DIR.'/manage.set/images/secret-ico.png\' align=\'absmiddle\' title=\'비밀글\' alt=\'비밀글\' />' : '';
        }

        // 수정 버튼
        function modify_btn($arr, $read, $category)
        {
            global $manage, $board_id;

            return (!$arr['dregdate']) ? '<a href=\'write'.$manage->lnk_def_param('&wrmode=modify&id='.$board_id.'&category='.urlencode($category).'&read='.$read).'\' class=\'btn1\'>수정</a>' : '';
        }

        // 답글 버튼
        function reply_btn($arr, $read, $category)
        {
            global $manage, $board_id;

            return ($arr['use_notice'] == 'Y' || Board_view::$boardconf['use_reply'] == 'N' || $arr['dregdate'] != '') ? '' : '<a href=\'write'.$manage->lnk_def_param('&wrmode=reply&id='.$board_id.'&category='.urlencode($category).'&read='.$read).'\' class=\'btn1\'>답글</a>';
        }

        // 리스트 버튼
        function list_btn($category)
        {
            global $manage, $board_id;

            return '<a href="board'.$manage->lnk_def_param('&id='.$board_id.'&category='.urlencode($category)).'" class="btn2">리스트</a>';
        }

        // 첨부 이미지 출력
        function print_imgfile($arr)
        {
            $files = array();
            for ($i = 1; $i <= 2; $i++) {
                $filetype = Func::get_filetype($arr['file'.$i]);
                $fileinfo = Func::get_fileinfo($arr['file'.$i]);

                if (Func::chkintd('match', $filetype,SET_IMGTYPE)) {
                    if ($fileinfo['storage'] == 'N' && file_exists(MOD_BOARD_DATA_PATH.'/'.Board_view::$boardconf['id'].'/thumb/'.$fileinfo['repfile'])) {
                        $files[$i] = '<img src=\''.PH_DOMAIN.MOD_BOARD_DATA_DIR.'/'.Board_view::$boardconf['id'].'/thumb/'.$fileinfo['repfile'].'\' alt=\'첨부된 이미지파일\' />';
                        if (Func::get_filetype($fileinfo['repfile']) == 'gif') $files[$i] = '<img src=\''.PH_DOMAIN.MOD_BOARD_DATA_DIR.'/'.Board_view::$boardconf['id'].'/'.$fileinfo['repfile'].'\' alt=\'첨부된 이미지파일\' />';

                    } else {
                        $files[$i] = '<img src=\''.$fileinfo['replink'].'\' alt=\'첨부된 이미지파일\' />';
                    }

                } else {
                    $files[$i] = null;
                }
            }

            return $files;
        }

        // 첨부파일명 및 용량(Byte) 출력
        function print_file_name($arr)
        {
            $files = array();

            for ($i = 1; $i <= 2; $i++) {
                if ($arr['file'.$i]) {
                    $fileinfo = Func::get_fileinfo($arr['file'.$i]);

                    $files[$i] = '
                    <a href=\''.MOD_BOARD_DIR.'/controller/file/down?board_id='.Board_view::$boardconf['id'].'&idx='.$arr['idx'].'&file='.$i.'\' target=\'_blank\'>'.Func::strcut($fileinfo['orgfile'],0,70).'</a>
                    <span class=\'byte\'>('.number_format($fileinfo['byte'] / 1024, 0).'K)</span>
                    <span class=\'cnt\'><strong>'.Func::number($arr['file'.$i.'_cnt']).'</strong> 회 다운로드</span>
                    ';

                } else {
                    $files[$i] = null;
                }
            }

            return $files;
        }

        // 회원 이름
        function print_writer($arr)
        {
            return ($arr['mb_idx'] != 0) ? '<a href="'.PH_MANAGE_DIR.'/member/modify?idx='.$arr['mb_idx'].'" target="_blank">'.$arr['writer'].'</a>' : $arr['writer'];
        }
    }

    public function make()
    {
        global $MB, $manage, $board_id, $board_id;

        $manage = new ManageFunc();
        $sql = new Pdosql();
        $sess = new Session();
        $boardlib = new Board_Library();

        $req = Method::request('get', 'id, mode, wrmode, read, page, where, keyword, category');

        $board_id = $req['id'];

        // add stylesheet & javascript
        Func::add_stylesheet(PH_MANAGE_DIR.'/css/content_view.css');

        // load config
        Board_view::$boardconf = $boardlib->load_conf($board_id);

        // 원본 글 불러옴
        $sql->query(
            "
            select member.mb_profileimg,
            ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=:col1 and likes>0 ) likes_cnt,
            ( select count(*) from {$sql->table("mod:board_like")} where id='$board_id' and data_idx=:col1 and unlikes>0 ) unlikes_cnt,
            board.*
            from {$sql->table("mod:board_data_".$board_id)} board
            left outer join {$sql->table("member")} member
            on board.mb_idx=member.mb_idx
            where board.idx=:col1
            ",
            array(
                $req['read']
            )
        );

        if ($sql->getcount() < 1) Func::err_back('해당 글이 존재하지 않습니다.');

        $arr = $sql->fetchs();

        $sql->specialchars = 0;
        $sql->nl2br = 0;

        $arr['article'] = $sql->fetch('article');

        // 게시물이 답글이며 회원에 대한 답글인 경우 부모글의 회원 idx 가져옴
        if ($arr['rn'] > 0 && $arr['pwd'] == '') {
            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$board_id)}
                where ln>:col1 and rn=:col2
                order by ln asc
                limit 1
                ",
                array(
                    $arr['ln'],
                    $arr['rn'] - 1
                )
            );
            $prt_mb_idx = $sql->fetch('mb_idx');
        }

        // view 노출
        if ($arr['dregdate']) {
            $is_dropbox_show = true;
            $is_article_show = false;

        } else {
            $is_dropbox_show = false;
            $is_article_show = true;
        }

        $is_file_show = array();

        for ($i = 1; $i <= 2; $i++) {
            $is_file_show[$i] = ($arr['file'.$i]) ? true : false;
        }

        $is_img_show = array();

        for ($i = 1; $i <= 2; $i++){
            $is_img_show[$i] = (print_imgfile($arr)[$i] != '') ? true : false;
        }

        $is_category_show = (Board_view::$boardconf['use_category'] == 'Y' && $arr['category'] && $arr['use_notice'] == 'N') ? true : false;
        $is_comment_show = (Board_view::$boardconf['use_comment'] == 'Y') ? true : false;
        $is_likes_show = (Board_view::$boardconf['use_likes'] == 'Y' && !$arr['dregdate']) ? true : false;
        $is_ftlist_show = (Board_view::$boardconf['use_list'] == 'Y') ? true : false;
        $is_seeklist_show = (Board_view::$boardconf['use_seek'] == 'Y') ? true : false;

        $arr['view'] = Func::number($arr['view']);
        $arr['date'] = Func::date($arr['regdate']);
        $arr['datetime'] = Func::datetime($arr['regdate']);
        $arr['likes_cnt'] = Func::number($arr['likes_cnt']);
        $arr['unlikes_cnt'] = Func::number($arr['unlikes_cnt']);

        $view = array();

        if (isset($arr)) {
            foreach ($arr as $key => $value) {
                $view[$key] = $value;
            }

        } else {
            $view = null;
        }

        $this->set('manage', $manage);
        $this->set('view', $view);
        $this->set('is_dropbox_show', $is_dropbox_show);
        $this->set('is_article_show', $is_article_show);
        $this->set('is_file_show', $is_file_show);
        $this->set('is_img_show', $is_img_show);
        $this->set('is_category_show', $is_category_show);
        $this->set('is_comment_show', $is_comment_show);
        $this->set('is_likes_show', $is_likes_show);
        $this->set('is_ftlist_show', $is_ftlist_show);
        $this->set('is_seeklist_show', $is_seeklist_show);
        $this->set('secret_ico', secret_ico($arr));
        $this->set('print_writer', print_writer($arr));
        $this->set('print_imgfile', print_imgfile($arr));
        $this->set('print_file_name', print_file_name($arr));
        $this->set('list_btn', list_btn($req['category']));
        $this->set('modify_btn', modify_btn($arr, $req['read'], $req['category']));
        $this->set('reply_btn', reply_btn($arr, $req['read'], $req['category']));
        $this->set('mode', $req['mode']);
        $this->set('wrmode', $req['wrmode']);
        $this->set('board_id', $board_id);
        $this->set('category', $req['category']);
        $this->set('read', $req['read']);
        $this->set('page', $req['page']);
        $this->set('where', $req['where']);
        $this->set('keyword', $req['keyword']);
        $this->set('thisuri', Func::thisuri());
    }

}
