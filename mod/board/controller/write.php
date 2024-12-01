<?php
namespace Module\Board;

use Corelib\Method;
use Corelib\Func;
use Corelib\Valid;
use Corelib\Session;
use Make\Library\Uploader;
use Make\Library\Imgresize;
use Make\Library\Mail;
use Make\Database\Pdosql;
use Module\Board\Library as Board_Library;
use Module\Alarm\Library as Alarm_Library;

//
// Module Controller
// ( Write )
//
class Write extends \Controller\Make_Controller {

    static private $show_pwdform = 0;
    static public $boardconf;

    public function init()
    {
        $this->layout()->view();
        $tpl = (Write::$show_pwdform == 0) ? MOD_BOARD_THEME_PATH.'/board/'.Write::$boardconf['theme'].'/write.tpl.php' : MOD_BOARD_THEME_PATH.'/board/'.Write::$boardconf['theme'].'/password.tpl.php';
        $this->layout()->view($tpl, false);
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

        // 공지글 옵션
        function opt_notice($arr, $wrmode)
        {
            global $MB;

            $notice_opt = '<label><input type="checkbox" name="use_notice" id="use_notice" value="checked" alt="공지글 작성" />공지글 작성</label>';

            if ($MB['level'] == 1 || $MB['level'] <= Write::$boardconf['ctr_level']) {
                if (isset($arr['use_notice']) && $arr['use_notice'] == 'Y') {
                    $notice_opt = '<label><input type="checkbox" name="use_notice" id="use_notice" value="checked" checked="checked" alt="공지글 작성" />공지글 작성</label>';

                } else if ((isset($arr['rn']) && $arr['rn'] > 0) || $wrmode == 'reply') {
                    $notice_opt = '';
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
                $secret_opt = '<label><input type="checkbox" name="use_secret" id="use_secret" value="checked" checked="checked" alt="비밀글 작성" />비밀글 작성</label>';

            } else if (Write::$boardconf['use_secret'] == 'Y') {
                $secret_opt = '<label><input type="checkbox" name="use_secret" id="use_secret" value="checked" alt="비밀글 작성" />비밀글 작성</label>';

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
                $email_opt = '<label><input type="checkbox" name="use_email" id="use_email" value="checked" checked="checked" alt="이메일로 답글 알림 수신" />이메일로 답글 알림 수신</label>';

            } else {
                $email_opt = '<label><input type="checkbox" name="use_email" id="use_email" value="checked" alt="이메일로 답글 알림 수신" />이메일로 답글 알림 수신</label>';
            }

            return $email_opt;
        }

        // 취소 버튼
        function cancel_btn($page, $category, $where, $keyword)
        {
            $keyword = (!empty($keyword)) ? urlencode($keyword) : '';
            return '<a href="'.Func::thisuri().Func::get_param_combine('page='.$page.'&category='.$category.'&where='.$where.'&keyword='.$keyword, '?').'" class="btn2">취소</a>';
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
            return Func::getbyte(Write::$boardconf['file_limit']);
        }

        // 게시글 임시 저장을 위한 hash 생성
        function make_temp_hash($arr)
        {
            if (!empty($arr)) {
                return $arr['hash'];

            } else {
                return Func::make_random_char(50);
            }
        }
    }

    public function make()
    {
        global $MB, $MOD_CONF;

        $sql = new Pdosql();
        $boardlib = new Board_Library();
        Write::$boardconf = $boardlib->load_conf($MOD_CONF['id']);

        $req = Method::request('get','mode, wrmode, read, page, where, keyword, category, temp_hash');

        $board_id = $MOD_CONF['id'];

        // 패스워드가 post로 submit 된 경우
        if (isset($_POST['s_password'])) {
            $s_req = Method::request('post', 's_mode, s_wrmode, s_read, s_page, s_category, s_where, s_keyword, s_password');
            $req['mode'] = $s_req['s_mode'];
            $req['wrmode'] = $s_req['s_wrmode'];
            $req['read'] = $s_req['s_read'];
            $req['page'] = $s_req['s_page'];
            $req['category'] = $s_req['s_category'];
            $req['where'] = $s_req['s_where'];
            $req['keyword'] = $s_req['s_keyword'];
        }

        //add title
        Func::add_title(Write::$boardconf['title'].' - 글 작성');

        // add stylesheet & javascript
        $boardlib->print_headsrc(Write::$boardconf['theme']);
        Write::$boardconf = $boardlib->load_conf($board_id);

        // 수정 or 답글인 경우 원본 글 불러옴
        if ($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') {
            $sql->query(
                "
                select board.*,ceil(board.ln) ceil_ln,
                ( select count(*) from {$sql->table("mod:board_data_".$board_id)} where `ln`<=((ceil_ln/1000)*1000) and `ln`>((ceil_ln/1000)*1000)-1000 and `rn`>0 ) reply_cnt
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
            $arr['article'] = $sql->fetch('article');

            if ($sql->getcount() < 1) Func::err_back('해당 글이 존재하지 않습니다.');

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

        // 임시 저장글 정보
        $sql->query(
            "
            select count(*) as total_count
            from {$sql->table("mod:board_temporary")}
            where `mb_idx`=:col1
            order by `regdate` desc
            limit 30
            ",
            array(
                $MB['idx']
            )
        );

        $my_temporary_count = $sql->fetch('total_count');

        // 임시 저장글 hash를 넘겨 받았다면 저장글 불러옴
        $my_temporary_arr = array();
        
        if ($req['temp_hash']) {
            $sql->query(
                "
                select *
                from {$sql->table("mod:board_temporary")}
                where `mb_idx`=:col1 and `hash`=:col2
                ",
                array(
                    $MB['idx'], $req['temp_hash']
                )
            );

            if ($sql->getcount() > 0) {
                $my_temporary_arr = $sql->fetchs();

                $sql->specialchars = 1;
                $sql->nl2br = 0;

                $my_temporary_arr['article'] = $sql->fetch('article');
            }
        }

        // check
        if (!$board_id) Func::err_back('게시판이 지정되지 않았습니다.');
        if ($MB['level'] > Write::$boardconf['write_level'] && $MB['level'] > Write::$boardconf['ctr_level']) Func::err_back('글 작성 권한이 없습니다.');

        if (!$req['wrmode'] || $req['wrmode'] == 'reply') {
            if (Write::$boardconf['write_point'] < 0) {
                if (!IS_MEMBER) Func::err_back('포인트 설정으로 인해 비회원은 글을 작성할 수 없습니다.');
                if ($MB['point'] < (0 - Write::$boardconf['write_point'])) Func::err_back('포인트가 부족하여 글을 작성할 수 없습니다.');
            }
        }
        if ($req['wrmode'] == 'reply' && Write::$boardconf['use_reply'] == 'N') Func::err_back('답변글을 등록할 수 없습니다.');

        // 삭제된 게시글인지 검사
        if (($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') && $arr['dregdate']) Func::err_back('삭제된 게시물입니다.');

        // 수정모드인 경우 권한 검사
        $wr_level = 1;
        
        if ($req['wrmode'] == 'modify') {

            if ($MB['level'] <= Write::$boardconf['ctr_level']) {
                $wr_level = 1;

            } else {
                if ($arr['mb_idx'] < 1 && !IS_MEMBER) {
                    $wr_level = 3;

                } else if ($arr['mb_idx'] == $MB['idx'] && $MB['level'] <= Write::$boardconf['write_level']) {
                    $wr_level = 1;

                } else {
                    $wr_level = 0;
                }
            }

            if ($wr_level == 0) Func::err_back('수정 권한이 없습니다.');
        }

        // 답글 모드인 경우 권한 검사
        if ($req['wrmode'] == 'reply') {
            if (($MB['level'] > Write::$boardconf['write_level'] && $MB['level'] > Write::$boardconf['ctr_level']) || $MB['level'] > Write::$boardconf['reply_level']) Func::err_back('답글 작성 권한이 없습니다.');
            if ($arr['use_notice'] == 'Y') Func::err_back('공지글에는 답글을 달 수 없습니다.');
        }

        // 패스워드가 submit된 경우 검사
        if (isset($s_req['s_password']) && empty($arr['mb_id'])) {
            if ($arr['pwd'] == $s_req['s_password']) {
                $wr_level = 1;

            } else {
                Func::err_back('입력한 비밀번호가 일치하지 않습니다.');
            }
        }

        // 권한이 없는 경우 경고창
        if ($wr_level == 0) Func::err_back('글 작성 권한이 없습니다.');

        // 패스워드 입력 폼 노출
        if ($req['wrmode'] == 'modify' && !IS_MEMBER && $wr_level != 1) {
            self::$show_pwdform = 1;
        }

        // 작성 폼 노출
        else {
            self::$show_pwdform = 0;

            if (!IS_MEMBER || ($req['wrmode'] == 'modify' && $arr['mb_idx'] == '0')) {
                $is_writer_show = true;
                $is_pwd_show = true;
                $is_email_show = true;

            } else {
                $is_writer_show = false;
                $is_pwd_show = false;
                $is_email_show = false;
            }

            $is_captcha_show = (!IS_MEMBER) ? true : false;
            $is_temporary_show = (!IS_MEMBER) ? false : true;

            // 첨부파일 input 노출 처리
            $is_file_dsp_cnt = (int)Write::$boardconf['use_file2'];
            $is_file_show = (Write::$boardconf['use_file1'] == 'Y') ? true : false;

            // 수정 모드인 경우 첨부파일 정보가 있는지 확인
            $is_filename_show = array();

            if ($req['wrmode'] == 'modify') {
                $sql->query(
                    "
                    select *
                    from {$sql->table("mod:board_files")}
                    where `id`=:col1 and `data_idx`=:col2
                    ",
                    array(
                        $board_id, $req['read']
                    )
                );

                $max_file_seq_no = 0;

                if ($sql->getcount() > 0) {
                    do {
                        $file_arr = $sql->fetchs();
    
                        $is_filename_show[$file_arr['file_seq']] = $file_arr;

                        $orgfile = Func::get_fileinfo($file_arr['file_name']);
                        $is_filename_show[$file_arr['file_seq']]['orgfile'] = $orgfile['orgfile'];

                        if ($file_arr['file_seq'] > $is_file_dsp_cnt) $is_file_dsp_cnt = $file_arr['file_seq'];
    
                    } while ($sql->nextRec());
                }
    
            }

            if (Write::$boardconf['use_category'] == 'Y' && Write::$boardconf['category'] != '' && (!$req['wrmode'] || $req['wrmode'] != 'reply') && (!isset($arr['rn']) || $arr['rn'] == 0) && (!isset($arr['reply_cnt']) || $arr['reply_cnt'] < 1)) {
                $is_category_show = true;

            } else {
                $is_category_show = false;
            }

            if (isset($arr) && !IS_MEMBER && $req['wrmode'] != 'modify') {
                $arr['pwd'] = '';
                $arr['email'] = '';
            }

            $write = array();
            if (isset($arr)) {
                foreach ($arr as $key => $value) {
                    $write[$key] = $value;
                }

            } else {
                $write = array('subject' => '', 'article' => '', 'writer' => '', 'pwd' => '', 'email' => '');

                for ($i = 1; $i <= 10; $i++) {
                    $write['data_'.$i] = '';
                }
            }

            // 임시저장글 정보를 불러온 경우 초기화
            if (!empty($my_temporary_arr)) {
                $write['subject'] = $my_temporary_arr['subject'];
                $write['article'] = $my_temporary_arr['article'];
            }

            $this->set('write', $write);
            $this->set('cancel_btn', cancel_btn($req['page'], $req['category'], $req['where'], $req['keyword']));
            $this->set('is_category_show', $is_category_show);
            $this->set('is_writer_show', $is_writer_show);
            $this->set('is_pwd_show', $is_pwd_show);
            $this->set('is_email_show', $is_email_show);
            $this->set('is_file_dsp_cnt', $is_file_dsp_cnt);
            $this->set('is_file_show', $is_file_show);
            $this->set('is_filename_show', $is_filename_show);
            $this->set('is_captcha_show', $is_captcha_show);
            $this->set('is_temporary_show', $is_temporary_show);
            $this->set('temp_hash', make_temp_hash($my_temporary_arr));
            $this->set('my_temporary_count', Func::number($my_temporary_count));
            $this->set('my_temporary_arr', $my_temporary_arr);

        }
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
        $this->set('captcha', Func::get_captcha('', 1));
        $this->set('top_source', Write::$boardconf['top_source']);
        $this->set('bottom_source', Write::$boardconf['bottom_source']);
    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board-writeForm');
        $form->set('type', 'multipart');
        $form->set('action', MOD_BOARD_DIR.'/controller/write/write-submit');
        $form->run();
    }

    public function pass_form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('id', 'board-pwdForm');
        $form->set('type', 'static');
        $form->set('target', 'view');
        $form->set('method', 'post');
        $form->run();
    }

}

//
// Controller for submit
// ( Write )
//
class Write_submit{

    public function init()
    {
        global $CONF, $MB, $board_id, $req, $ufile, $wr_opt, $org_arr;

        $boardlib = new Board_Library();
        $uploader = new Uploader();
        $imgresize = new Imgresize();
        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'thisuri, board_id, wrmode, read, page, where, keyword, category_ed, use_html, category, use_notice, use_secret, use_email, writer, password, email, subject, article, file_del, captcha, request, wdate_date, wdate_h, wdate_i, wdate_s, data_1, data_2, data_3, data_4, data_5, data_6, data_7, data_8, data_9, data_10');
        $f_req = Method::request('file', 'file');

        $board_id = $req['board_id'];

        // load config
        Write::$boardconf = $boardlib->load_conf($board_id);

        // 수정 or 답글인 경우 원본 글 가져옴
        if ($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') {
            $sql->query(
                "
                select board.*,ceil(board.ln) ceil_ln,
                ( select count(*) from {$sql->table("mod:board_data_".$board_id)} where `ln`<=((ceil_ln/1000)*1000) and `ln`>((ceil_ln/1000)*1000)-1000 and `rn`>0 ) reply_cnt
                from {$sql->table("mod:board_data_".$board_id)} board
                where board.idx=:col1
                ",
                array(
                    $req['read']
                )
            );
            $org_arr = $sql->fetchs();

            // 첨부파일 정보 가져옴
            $sql->query(
                "
                select *
                from {$sql->table("mod:board_files")}
                where `id`=:col1 and `data_idx`=:col2
                ",
                array(
                    $board_id, $req['read']
                )
            );
            if ($sql->getcount() > 0) {
                do {
                    $org_arr['files'][$sql->fetch('file_seq')] = $sql->fetchs();

                } while ($sql->nextRec());
            }
        }

        // 수정 or 답글인 경우 삭제된 게시글인지 검사
        if (($req['wrmode'] == 'modify' || $req['wrmode'] == 'reply') && $org_arr['dregdate']) Func::err_back('삭제된 게시글입니다.');

        // 옵션값 처리
        $wr_opt = array();
        if ($req['use_notice'] == 'checked') {
            $wr_opt['notice'] = 'Y';
            $wr_opt['email'] = 'N';

        } else {
            $wr_opt['notice'] = 'N';
        }

        if (Write::$boardconf['use_secret'] == 'Y') {
            $wr_opt['secret'] = ($req['use_secret'] == 'checked') ? 'Y' : 'N';

        } else if(!$req['wrmode'] || $req['wrmode'] == 'write') {
            $wr_opt['secret'] = 'N';

        } else {
            $wr_opt['secret'] = $org_arr['use_secret'];
        }

        $wr_opt['email'] = ($req['use_email'] == 'checked') ? 'Y' : 'N';

        // 수정모드인 경우 여분필드 처리
        if ($req['wrmode'] == 'modify') {
            for ($i = 1 ;$i <= 10; $i++) {
                if (!isset($req['data_'.$i])) $req['data_'.$i] = $org_arr['data_'.$i];
            }
        }
        
        // 글 작성 권한 검사
        if ($MB['level'] > Write::$boardconf['write_level'] && $MB['level'] > Write::$boardconf['ctr_level']) Valid::error('','글 작성 권한이 없습니다.');

        // 수정모드인 경우 수정 권한 검사
        if ($req['wrmode'] == 'modify' && $org_arr['mb_idx'] != $MB['idx'] && $MB['level'] > Write::$boardconf['ctr_level']) Valid::error('','글 수정 권한이 없습니다.');

        // 기본 입력 항목 검사
        Valid::get(
            array(
                'input' => 'subject',
                'value' => $req['subject']
            )
        );

        if (Write::$boardconf['article_min_len'] > 0) {
            Valid::get(
                array(
                    'input' => 'article',
                    'value' => $req['article'],
                    'check' => array(
                        'minlen' => Write::$boardconf['article_min_len'],
                        'chkhtml' => true,
                        'null' => false,
                    )
                )
            );

        } else {
            Valid::get(
                array(
                    'input' => 'article',
                    'value' => $req['article'],
                    'check' => array(
                        'chkhtml' => true,
                        'null' => true,
                    )
                )
            );
        }

        if (!IS_MEMBER) {

            Valid::get(
                array(
                    'input' => 'writer',
                    'value' => $req['writer'],
                    'check' => array(
                        'defined' => 'nickname'
                    )
                )
            );
            Valid::get(
                array(
                    'input' => 'password',
                    'value' => $req['password'],
                    'check' => array(
                        'defined' => 'password'
                    )
                )
            );

            if ($wr_opt['email'] == 'Y') {
                Valid::get(
                    array(
                        'input' => 'email',
                        'value' => $req['email'],
                        'check' => array(
                            'defined' => 'email'
                        )
                    )
                );
            }

            if (!Func::chk_captcha($req['captcha'])) {
                Valid::set(
                    array(
                        'return' => 'error',
                        'input' => 'captcha',
                        'err_code' => 'NOTMATCH_CAPTCHA'
                    )
                );
                Valid::turn();
            }
        }

        if (isset($f_req['file1']) && isset($f_req['file2']) && $f_req['file1']['name'] == $f_req['file2']['name']) Valid::error('', '동일한 파일을 업로드 할 수 없습니다.');

        // 수정모드인 경우 검사 (접속자가 회원이고, 원글은 비회원 글인 경우 추가 입력 항목 검사)
        if ($req['wrmode'] == 'modify' && IS_MEMBER && $org_arr['mb_idx'] == 0) {

            Valid::get(
                array(
                    'input' => 'writer',
                    'value' => $req['writer'],
                    'check' => array(
                        'defined' =>'nickname'
                    )
                )
            );
            Valid::get(
                array(
                    'input' => 'password',
                    'value' => $req['password'],
                    'check' => array(
                        'defined' =>'password'
                    )
                )
            );

            if ($wr_opt['email'] == 'Y' || $req['email'] != '') {
                Valid::get(
                    array(
                        'input' => 'email',
                        'value' => $req['email'],
                        'check' => array(
                            'defined' =>'email'
                        )
                    )
                );
            }
        }

        // 글 작성인 경우, 이미 같은 내용의 글이 존재하는지 검사
        if (!$req['wrmode'] || $req['wrmode'] == 'reply') {
            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$board_id)}
                where `article`=:col1
                ",
                array(
                    $req['article']
                )
            );
            if ($sql->getcount() > 0) Valid::error('article', '이미 같은 내용의 글이 존재합니다.');
        }

        // 글 작성 포인트 조정
        if (!$req['wrmode'] || $req['wrmode'] == 'reply') {
            if (Write::$boardconf['write_point'] < 0) {
                if (!IS_MEMBER) Valid::error('', '포인트 설정으로 인해 비회원은 글을 작성할 수 없습니다.');
                if ($MB['point'] < (0 - Write::$boardconf['write_point'])) Valid::error('', '포인트가 부족하여 글을 작성할 수 없습니다.');

                $point = 0 - Write::$boardconf['write_point'];
                Func::set_mbpoint(
                    array(
                        'mb_idx' => $MB['idx'],
                        'mode' => 'out',
                        'point' => $point,
                        'msg' => '게시판 글 작성 ('.Write::$boardconf['title'].')'

                    )
                );

            } else if (Write::$boardconf['write_point'] > 0) {
                Func::set_mbpoint(
                    array(
                        'mb_idx' => $MB['idx'],
                        'mode' => 'in',
                        'point' => Write::$boardconf['write_point'],
                        'msg' => '게시판 글 작성 ('.Write::$boardconf['title'].')'
                    )
                );
            }
        }

        //월별로 디렉토리 구분
        $upload_dir = date('ym');

        // 첨부파일 저장
        $uploader->path = MOD_BOARD_DATA_PATH.'/'.$board_id.'/'.$upload_dir;
        $uploader->chkpath();

        $ufile = array();
        $ufile_name = array();

        if (isset($f_req['file']) && !empty($f_req['file'])) {
            foreach ($f_req['file']['name'] as $key => $value) {
                $ufile[$key] = array(
                    'name' => $value,
                    'tmp_name' => $f_req['file']['tmp_name'][$key],
                    'size' => $f_req['file']['size'][$key]
                );
                
                $tmp_name = $ufile[$key];
    
                if (isset($tmp_name) && !empty($tmp_name)) {
                    $uploader->file = $tmp_name;
    
                    if ($uploader->chkfile('match') === true) Valid::error('', '첨부파일'.$key.' : '.ERR_MSG_8);
                    if ($uploader->chkbyte(Write::$boardconf['file_limit']) === false) Valid::error('', '첨부파일'.$key.' : 허용 파일 용량을 초과합니다.');
    
                    $ufile[$key]['ufile_name'] = $uploader->replace_filename($ufile[$key]['name']);
                    array_push($ufile_name, $ufile[$key]['ufile_name']);
    
                    if (!$uploader->upload($ufile[$key]['ufile_name'])) Valid::error('', '첨부파일'.$key.' : 업로드 실패');
                }
            }
        }

        // 썸네일 생성
        if ($CONF['use_s3'] == 'N') {
            $uploader->path = MOD_BOARD_DATA_PATH.'/'.$board_id.'/'.$upload_dir.'/thumb';
            $uploader->chkpath();

            for ($i = 0; $i < count($ufile_name); $i++) {
                $intd = explode(',', SET_IMGTYPE);
                $f_type = Func::get_filetype($ufile_name[$i]);

                for ($j = 0; $j <= count($intd) - 1; $j++) {
                    if ($f_type == trim($intd[$j])) {
                        $imgresize->set(
                            array(
                                'orgimg' => MOD_BOARD_DATA_PATH.'/'.$board_id.'/'.$upload_dir.'/'.$ufile_name[$i],
                                'newimg' => $uploader->path.'/'.$ufile_name[$i],
                                'width' => 800
                            )
                        );
                        $imgresize->make();
                    }
                }
            }
        }

        // 수정모드인 경우 기존 파일 & 썸네일 삭제
        if ($req['wrmode'] == 'modify') {
            for ($i = 1; $i <= (int)Write::$boardconf['use_file2']; $i++) {

                $get_delete_checked = (isset($req['file_del'][$i]) && $req['file_del'][$i] === 'checked') ? true : false;
                $get_delete_not_checked = (!isset($req['file_del'][$i]) || $req['file_del'][$i] !== 'checked') ? true : false;
                $is_has_orgfile = (isset($org_arr['files'][$i]) && !empty($org_arr['files'][$i])) ? true : false;
                $is_uploaded_newfile = (isset($ufile[$i])) ? true : false;
                
                // 기존 파일을 삭제한뒤 새로 업로드한 파일로 대체
                if ($get_delete_checked || ($is_uploaded_newfile && $is_has_orgfile && $get_delete_not_checked)) {

                    // file lookup
                    $fileinfo = Func::get_fileinfo($org_arr['files'][$i]['file_name']);

                    // 원본 파일 삭제
                    $uploader->path = PH_DATA_PATH.$fileinfo['filepath'];
                    $uploader->drop($org_arr['files'][$i]['file_name']);

                    // 썸네일이 있다면 삭제
                    if ($CONF['use_s3'] == 'N' && $uploader->isfile(PH_DATA_PATH.$fileinfo['filepath'].'/thumb/'.$org_arr['files'][$i]['file_name'])) {
                        $uploader->path = PH_DATA_PATH.$fileinfo['filepath'].'/thumb';
                        $uploader->drop($org_arr['files'][$i]['file_name'], false);
                    }
                    
                    $sql->query(
                        "
                        delete
                        from {$sql->table("mod:board_files")}
                        where `id`=:col1 and `data_idx`=:col2 and `file_seq`=:col3
                        ",
                        array(
                            $board_id, $req['read'], $i
                        )
                    );
                }

                // 기존에 첨부된 파일이 있지만 아무것도 하지 않았을 때
                if ($is_has_orgfile && !$is_uploaded_newfile && $get_delete_not_checked) {
                    $ufile[$i]['ufile_name'] = $org_arr['files'][$i]['file_name'];

                    $modified_files[$i] = 'modify';
                }
            }
        }

        // 첨부파일 종류 기록
        $req[0]['ufile_icon_type'] = '';

        ksort($ufile); // key 오름차순으로 순서 정리

        if (!empty($ufile)) {
            $req[0]['ufile_icon_type'] = 'file';

            foreach ($ufile as $key => $value) {
                $file_type = Func::get_filetype($value['ufile_name']);
                if (Func::chkintd('match', $file_type, SET_IMGTYPE)) $req[0]['ufile_icon_type'] = 'image';
            }
        }

        // 대표이미지로 사용할 이미지 지정
        $req[0]['ufile_tmb_filename'] = '';

        preg_match(REGEXP_IMG, Func::htmldecode($req['article']), $match);

        if (!empty($ufile)) {
            foreach ($ufile as $key => $value) {
                $file_type = Func::get_filetype($value['ufile_name']);

                if (!$req[0]['ufile_tmb_filename'] && Func::chkintd('match', $file_type, SET_IMGTYPE)) {
                    $req[0]['ufile_tmb_filename'] = $value['ufile_name'];
                }
            }
        }

        if (!$req[0]['ufile_tmb_filename']) {
            $req[0]['ufile_tmb_filename'] = (isset($match[1])) ? basename($match[1]) : '';
        }

        // wrmode 별 처리
        switch ($req['wrmode']) {

            case 'reply' :
                $this->get_reply();
                break;

            case 'modify' :
                $this->get_modify();
                break;

            default :
                $this->get_write();
                break;
        }
    }

    //
    // 새로운 글 작성
    //
    private function get_write()
    {
        global $MODULE_BOARD_CONF, $MB, $req, $ufile, $wr_opt, $board_id;

        $sql = new Pdosql();

        // ln값 처리
        $sql->query(
            "
            select max(`ln`)+1000 as ln_max
            from {$sql->table("mod:board_data_".$board_id)}
            ", []
        );

        $ln_arr = array();
        $ln_arr['ln_max'] = $sql->fetch('ln_max');

        if (!$ln_arr['ln_max']) $ln_arr['ln_max'] = 1000;

        $ln_arr['ln_max'] = ceil($ln_arr['ln_max'] / 1000) * 1000;

        // 회원인 경우 회원 정보를 필드에 입력
        if (IS_MEMBER) {
            $req['email'] = $MB['email'];
            $req['writer'] = $MB['name'];
        }

        // Manager 에서 등록한 경우 날짜 설정
        $wdate = date('Y-m-d H:i:s');
        if (isset($req['request']) && $req['request'] == 'manage') {
            if ($req['wdate_date']) $wdate = $req['wdate_date'].' '.$req['wdate_h'].':'.$req['wdate_i'].':'.$req['wdate_s'];
        }

        // insert
        $sql->query(
            "
            insert into {$sql->table("mod:board_data_".$board_id)}
            (`category`, `mb_idx`, `mb_id`, `writer`, `pwd`, `email`, `article`, `subject`, `use_secret`, `use_notice`, `use_html`, `use_email`, `ip`, `ln`, `rn`, `file1`, `file2`, `data_1`, `data_2`, `data_3`, `data_4`, `data_5`, `data_6`, `data_7`, `data_8`, `data_9`, `data_10`, `regdate`)
            values
            (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, 'Y', :col11, '".MB_REMOTE_ADDR."', :col12, :col13, :col14, :col15, :col16, :col17, :col18, :col19, :col20, :col21, :col22, :col23, :col24, :col25, :col26)
            ",
            array(
                $req['category'], $MB['idx'], $MB['id'], $req['writer'], $req['password'], $req['email'], $req['article'], $req['subject'], $wr_opt['secret'], $wr_opt['notice'], $wr_opt['email'], $ln_arr['ln_max'], 0, $req[0]['ufile_icon_type'], $req[0]['ufile_tmb_filename'],
                $req['data_1'], $req['data_2'], $req['data_3'], $req['data_4'], $req['data_5'], $req['data_6'], $req['data_7'], $req['data_8'], $req['data_9'], $req['data_10'], $wdate
            )
        );

        // 작성된 글의 idx를 다시 가져옴
        $sql->query(
            "
            select max(`idx`) as max_idx
            from {$sql->table("mod:board_data_".$board_id)}
            where `writer`=:col1
            ",
            array(
                $req['writer']
            )
        );
        $inserted_data_idx = $sql->fetch('max_idx');

        // 첨부파일 정보 insert
        foreach ($ufile as $key => $value) {
            $sql->query(
                "
                insert into {$sql->table("mod:board_files")}
                (`id`, `data_idx`, `file_seq`, `file_name`, `regdate`)
                values
                (:col1, :col2, :col3, :col4, now())
                ",
                array(
                    $board_id, $inserted_data_idx, $key, $value['ufile_name']
                )
            );
        }

        // 관리자 Dashboard 소식 등록
        if (!isset($req['request'])) {
            $req['thisuri'] = htmlspecialchars($req['thisuri']);
            $req['thisuri'] = str_replace(array("'", '"'), '', $req['thisuri']);
            
            $feed_uri = (PH_DIR) ? str_replace(PH_DIR, '', $req['thisuri']) : $req['thisuri'];
    
            if (Write::$boardconf['use_mng_feed'] == 'Y') {
                Func::add_mng_feed(
                    array(
                        'from' => $MODULE_BOARD_CONF['title'],
                        'msg' => '<strong>'.$req['writer'].'</strong>님이 <strong>'.Write::$boardconf['title'].'</strong> 게시판에 새로운 글을 등록했습니다.',
                        'link' => $feed_uri.'/'.$inserted_data_idx
                    )
                );
            }
        }

        // return
        $req['category'] = (!empty($req['category'])) ? urlencode($req['category']) : '';

        if ($sql->getcount() > 0) {
            $return_url = $req['thisuri'].'/'.$inserted_data_idx.Func::get_param_combine('?category='.$req['category'], '?');

        } else {
            $return_url = $req['thisuri'].Func::get_param_combine('?category='.urlencode($req['category']), '?');
        }

        if (isset($req['request']) && $req['request'] == 'manage') $return_url = './board?id='.$board_id.'&category='.$req['category'];

        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => $return_url
            )
        );
        Valid::turn();
    }

    //
    // 글 수정
    //
    private function get_modify()
    {
        global $MB, $req, $org_arr, $ufile, $wr_opt, $board_id;

        $sql = new Pdosql();

        // 공지사항 옵션 체크한 경우 답글이 있는지
        if ($req['use_notice'] == 'checked') {

            // 최소/최대 ln값 구함
            $ln_min = (int)(ceil($org_arr['ln'] / 1000) * 1000) - 1000;
            $ln_max = (int)(ceil($org_arr['ln'] / 1000) * 1000);

            $sql->query(
                "
                select *
                from {$sql->table("mod:board_data_".$board_id)}
                where `ln`>:col1 and `ln`<=:col2
                ",
                array(
                    $ln_min, $ln_max
                )
            );

            if ($sql->getCount() > 1) Valid::error('', '답글이 있는 게시글은 공지사항 옵션을 사용할 수 없습니다.');
        }

        // Category 처리
        $category = ($org_arr['reply_cnt'] > 0) ? $org_arr['category'] : $req['category'];

        // writer 처리
        $req['writer'] = ($org_arr['mb_idx'] == $MB['idx'] && IS_MEMBER) ? $MB['name'] : $org_arr['writer'];

        // email & password 처리
        if (IS_MEMBER && $org_arr['mb_idx'] != 0) {
            $req['email'] = $org_arr['email'];
            $req['password'] = $org_arr['pwd'];
        }

        // manager에서 등록한 경우 날짜 설정
        $wdate = $org_arr['regdate'];
        if (isset($req['request']) && $req['request'] == 'manage' && $req['wdate_date']) {
            $wdate = $req['wdate_date'].' '.$req['wdate_h'].':'.$req['wdate_i'].':'.$req['wdate_s'];
        }

        // update
        $sql->query(
            "
            update {$sql->table("mod:board_data_".$board_id)}
            set `category`=:col2, `writer`=:col3, `pwd`=:col4, `email`=:col5, `article`=:col6, `subject`=:col7, `use_secret`=:col8, `use_notice`=:col9,
            use_html='Y', `use_email`=:col10, `ip`='".MB_REMOTE_ADDR."', `file1`=:col11, `file2`=:col12, `regdate`=:col13, `data_1`=:col14, `data_2`=:col15, `data_3`=:col16, `data_4`=:col17, `data_5`=:col18, `data_6`=:col19, `data_7`=:col20, `data_8`=:col21, `data_9`=:col22, `data_10`=:col23
            where `idx`=:col1
            ",
            array(
                $req['read'], $category, $req['writer'], $req['password'], $req['email'], $req['article'], $req['subject'],
                $wr_opt['secret'], $wr_opt['notice'], $wr_opt['email'], $req[0]['ufile_icon_type'], $req[0]['ufile_tmb_filename'], $wdate, $req['data_1'], $req['data_2'], $req['data_3'], $req['data_4'], $req['data_5'],
                $req['data_6'], $req['data_7'], $req['data_8'], $req['data_9'], $req['data_10']
            )
        );
        
        // 첨부파일 정보 update
        foreach ($ufile as $key => $value) {
            
            // 기존 첨부파일 정보 있는지 확인
            $sql->query(
                "
                select count(*) total_count
                from {$sql->table("mod:board_files")}
                where `id`=:col1 and `data_idx`=:col2 and `file_seq`=:col3
                ",
                array(
                    $board_id, $req['read'], $key
                )
            );

            // 기존에 첨부파일 정보가 있다면 새로운 파일로 갱신
            if ($sql->fetch('total_count') > 0) {
                $sql->query(
                    "
                    update {$sql->table("mod:board_files")}
                    set `file_name`=:col4, regdate=now()
                    where `id`=:col1 and `data_idx`=:col2 and `file_seq`=:col3 and `file_name`!=:col4
                    ",
                    array(
                        $board_id, $req['read'], $key, $value['ufile_name']
                    )
                );   
            }
            
            // 기존에 첨부파일 정보가 없다면 신규 등록
            else {
                $sql->query(
                    "
                    insert into {$sql->table("mod:board_files")}
                    (`id`, `data_idx`, `file_seq`, `file_name`, `regdate`)
                    values
                    (:col1, :col2, :col3, :col4, now())
                    ",
                    array(
                        $board_id, $req['read'], $key, $value['ufile_name']
                    )
                );
            }
        }

        // 조회수 session
        Session::set_sess('BOARD_VIEW_'.$req['read'], $req['read']);

        // return
        $return_url = $req['thisuri'].'/'.$req['read'].Func::get_param_combine('page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category_ed']), '?');
        if (isset($req['request']) && $req['request'] == 'manage') $return_url = './board-view?id='.$board_id.'&read='.$req['read'].'&page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category_ed']);

        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => $return_url
            )
        );
        Valid::turn();
    }

    //
    // 답글 작성
    //
    private function get_reply()
    {
        global $MODULE_BOARD_CONF, $MB, $req, $org_arr, $ufile, $wr_opt, $board_id;

        $sql = new Pdosql();
        $mail = new Mail();
        $Alarm_Library = new Alarm_Library();

        // ln값 처리
        $ln_max = (int)$org_arr['ln'];
        $ln_min = (int)(ceil($org_arr['ln'] / 1000) * 1000) - 1000;
        $ln_me = (int)$org_arr['ln'] - 1;

        $sql->query(
            "
            update {$sql->table("mod:board_data_".$board_id)}
            set `ln`=`ln`-1
            where `ln`<:col1 and `ln`>:col2 and `rn`>0
            ",
            array(
                $ln_max, $ln_min
            )
        );

        // rn값 처리
        $sql->query(
            "
            select `rn`+1 as rn_max
            from {$sql->table("mod:board_data_".$board_id)}
            where `idx`=:col1
            ",
            array(
                $req['read']
            )
        );

        $rn_arr = array();
        $rn_arr['rn_max'] = $sql->fetch('rn_max');

        // 회원인 경우 정보를 필드에 기록
        if (IS_MEMBER) {
            $req['email'] = $MB['email'];
            $req['writer'] = $MB['name'];
        }

        // 비회원의 비밀글에 대한 답글인 경우 원본글의 비밀번호를 기록
        if ($org_arr['use_secret'] == 'Y' && $org_arr['mb_idx'] == 0) $req['password'] = $org_arr['pwd'];

        // insert
        $sql->query(
            "
            insert into {$sql->table("mod:board_data_".$board_id)}
            (`category`, `mb_idx`, `mb_id`, `writer`, `pwd`, `email`, `article`, `subject`, `file1`, `file2`, `use_secret`, `use_notice`, `use_html`, `use_email`, `ip`, `regdate`, `ln`, `rn`, `data_1`, `data_2`, `data_3`, `data_4`, `data_5`, `data_6`, `data_7`, `data_8`, `data_9`, `data_10`)
            values
            (:col1, :col2, :col3, :col4, :col5, :col6, :col7, :col8, :col9, :col10, :col11, :col12, 'Y', :col13, '".MB_REMOTE_ADDR."', now(), :col14, :col15, :col16, :col17, :col18, :col19, :col20, :col21, :col22, :col23, :col24, :col25)
            ",
            array(
                $org_arr['category'], $MB['idx'], $MB['id'], $req['writer'], $req['password'], $req['email'], $req['article'], $req['subject'], $req[0]['ufile_icon_type'], $req[0]['ufile_tmb_filename'], $wr_opt['secret'], $wr_opt['notice'], $wr_opt['email'], $ln_me, $rn_arr['rn_max'],
                $req['data_1'], $req['data_2'], $req['data_3'], $req['data_4'], $req['data_5'], $req['data_6'], $req['data_7'], $req['data_8'], $req['data_9'], $req['data_10']
            )
        );

        // 작성된 글의 idx를 다시 가져옴
        $sql->query(
            "
            select max(`idx`) as max_idx
            from {$sql->table("mod:board_data_".$board_id)}
            where `writer`=:col1 and `subject`=:col2 and `article`=:col3
            ",
            array(
                $req['writer'], $req['subject'], $req['article']
            )
        );

        // 원본글이 답글 이메일 수신 옵션이 켜져 있는 경우 원본글 작성자에게 메일 발송
        if ($org_arr['use_email'] == 'Y') {
            $memo = '
                <strong>'.Write::$boardconf['title'].'</strong>에 게시한<br /><br />
                회원님의 게시글에 답글이 달렸습니다.<br />
                아래 주소를 클릭하여 확인 할 수 있습니다.<br /><br />

                <a href=\''.PH_DOMAIN.$req['thisuri'].'/'.$sql->fetch('max_idx').Func::get_param_combine('category='.urlencode($req['category_ed']), '?').'\'>'.PH_DOMAIN.$req['thisuri'].'/'.$sql->fetch('max_idx').Func::get_param_combine('category='.urlencode($req['category_ed']), '?').'</a>';

            $mail->set(
                array(
                    'to' => array(
                        [
                            'email' => $org_arr['email']
                        ]
                    ),
                    'subject' => '회원님의 게시글에 답글이 달렸습니다.',
                    'memo' => str_replace('\"','"',stripslashes($memo))
                )
            );
            $mail->send();
        }

        // 조회수 session
        Session::set_sess('BOARD_VIEW_'.$sql->fetch('max_idx'), $sql->fetch('max_idx'));

        // 관리자 최근 피드에 등록
        $feed_uri = (PH_DIR) ? str_replace(PH_DIR, '', $req['thisuri']) : $req['thisuri'];

        if (Write::$boardconf['use_mng_feed'] == 'Y') {
            Func::add_mng_feed(
                array(
                    'from' => $MODULE_BOARD_CONF['title'],
                    'msg' => '<strong>'.$req['writer'].'</strong>님이 <strong>'.Write::$boardconf['title'].'</strong> 게시판에 새로운 답글을 등록했습니다.',
                    'link' => $feed_uri.'/'.$sql->fetch('max_idx')
                )
            );
        }

        // 원글 작성자에게 알림 발송
        if ($req['wrmode'] == 'reply' && $org_arr['mb_idx'] > 0 && $org_arr['mb_idx'] != $MB['idx']) {
            $Alarm_Library->get_add_alarm(
                array(
                    'msg_from' => '게시판 ('.Write::$boardconf['title'].')',
                    'from_mb_idx' => $MB['idx'],
                    'to_mb_idx' => $org_arr['mb_idx'],
                    'memo' => '<strong>'.$req['writer'].'</strong>님이 회원님의 게시글에 답글을 작성했습니다.',
                    'link' => $feed_uri.'/'.$sql->fetch('max_idx')
                )
            );
        }

        // return
        if ($sql->getcount() > 0) {
            $return_url = $req['thisuri'].'/'.$sql->fetch('max_idx').Func::get_param_combine('page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category_ed']), '?');

        } else {
            $return_url = $req['thisuri'].Func::get_param_combine('?category='.urlencode($req['category']), '?');
        }

        if (isset($req['request']) && $req['request'] == 'manage') $return_url = './board?id='.$board_id.'&category='.urlencode($req['category_ed']).'&page='.$req['page'].'&where='.$req['where'].'&keyword='.$req['keyword'].'&category='.urlencode($req['category_ed']);

        Valid::set(
            array(
                'return' => 'alert->location',
                'location' => $return_url
            )
        );
        Valid::turn();
    }

}

//
// Controller for submit
// ( Write-temprary )
//
class Write_temporary_submit{

    public function init(){
        global $CONF, $MB;

        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'board_id, temp_hash, subject, article');

        if (!IS_MEMBER) Valid::error('', '임시글 저장은 회원만 가능합니다.');

        if (!$req['temp_hash']) Valid::error('', ERR_MSG_1);
        
        // 기본 입력 항목 검사
        Valid::get(
            array(
                'input' => 'subject',
                'value' => $req['subject']
            )
        );

        Valid::get(
            array(
                'input' => 'article',
                'value' => $req['article'],
                'check' => array(
                    'chkhtml' => true,
                    'null' => true,
                )
            )
        );

        // 동일 hash의 임시글이 있는지 검사
        $sql->query(
            "
            select count(*) as total_count
            from {$sql->table("mod:board_temporary")}
            where `mb_idx`=:col1 and `hash`=:col2
            ",
            array(
                $MB['idx'], $req['temp_hash']
            )
        );

        $temp_total_count = $sql->fetch('total_count');

        // 임시글 처리 (insert)
        if ($temp_total_count < 1) {
            $sql->query(
                "
                insert into {$sql->table("mod:board_temporary")}
                (`id`, `mb_idx`, `hash`, `subject`, `article`, `regdate`)
                values
                (:col1, :col2, :col3, :col4, :col5, now())
                ",
                array(
                    $req['board_id'], $MB['idx'], $req['temp_hash'], $req['subject'], $req['article']
                )
            );

        // 임시글 처리 (update)
        } else {
            $sql->query(
                "
                update {$sql->table("mod:board_temporary")}
                set `id`=:col3, `subject`=:col4, `article`=:col5, `regdate`=now()
                where `mb_idx`=:col1 and `hash`=:col2
                ",
                array(
                    $MB['idx'], $req['temp_hash'], $req['board_id'], $req['subject'], $req['article']
                )
            );
        }

        // 자신의 임시글 개수 가져옴
        $sql->query(
            "
            select count(*) as total_count
            from {$sql->table("mod:board_temporary")}
            where `mb_idx`=:col1
            order by `regdate` desc
            limit 30
            ",
            array(
                $MB['idx']
            )
        );

        $my_temp_total_count = $sql->fetch('total_count');

        Valid::set(
            array(
                'return' => 'callback-txt',
                'element' => '#board-temporary-btnbox .load-btn strong',
                'msg' => Func::number($my_temp_total_count)
            )
        );
        Valid::turn();
     
    }

}
