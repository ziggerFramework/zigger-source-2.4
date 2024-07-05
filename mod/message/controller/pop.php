<?php
namespace Module\Message;

use Corelib\Method;
use Corelib\Valid;
use Corelib\Func;
use Make\Database\Pdosql;

//
// Module Controller
// ( Message_send )
//
class Message_send extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_MESSAGE_THEME_PATH.'/message-send.tpl.php');
    }

    public function make()
    {
        global $MB;

        $req = Method::request('get', 'to_mb_id, reply_parent_hash');

        $is_mbinfo_show = true;

        if (!IS_MEMBER) $is_mbinfo_show = false;

        $this->set('to_mb_id', $req['to_mb_id']);
        $this->set('reply_parent_hash', $req['reply_parent_hash']);
        $this->set('is_mbinfo_show', $is_mbinfo_show);

    }

    public function form()
    {
        $form = new \Controller\Make_View_Form();
        $form->set('type', 'html');
        $form->set('action', MOD_MESSAGE_DIR.'/controller/pop/message-send-submit');
        $form->run();
    }

}

//
// Controller for submit
// ( Message_send )
//
class Message_send_submit {

    public function init()
    {
        global $MB;

        $sql = new Pdosql();

        Method::security('referer');
        Method::security('request_post');
        $req = Method::request('post', 'to_mb_id, article, reply_parent_hash');

        // 관리 권한 검사
        if (!IS_MEMBER) Valid::error('', '메시지를 발송할 권한이 없습니다.');

        // 회원 아이디 검증
        Valid::get(
            array(
                'input' => 'to_mb_id',
                'value' => $req['to_mb_id'],
                'check' => array(
                    'null' => false,
                    'defined' => 'id'
                )
            )
        );

        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where `mb_id`=:col1 and `mb_dregdate` is null
            ",
            array(
                $req['to_mb_id']
            )
        );
        if ($sql->getcount() < 1) Valid::error('to_mb_id', '존재하지 않는 회원 아이디 입니다.');

        $to_mb_idx = $sql->fetch('mb_idx');

        // 내용 검증
        Valid::get(
            array(
                'input' => 'article',
                'value' => $req['article'],
                'check' => array(
                    'null' => false,
                    'minlen' => 5,
                    'maxlen' => 1000
                )
            )
        );

        // parent_idx 처리
        $reply_parent_hash = null;
        if ($req['reply_parent_hash']) $reply_parent_hash = $req['reply_parent_hash'];

        // 메시지 발송
        $sql->query(
            "
            insert into {$sql->table("mod:message")}
            (`hash`, `from_mb_idx`, `to_mb_idx`, `parent_hash`, `article`, `regdate`)
            values
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                Func::make_random_char(), MB_IDX, $to_mb_idx, $reply_parent_hash, $req['article']
            )
        );

        // message함 parent_idx 정렬
        $sql->query(
            "
            update {$sql->table("mod:message")}
            set `parent_hash` = hash
            where `parent_hash` is null
            ", []
        );

        // return
        Valid::set(
            array(
                'return' => 'callback',
                'function' => 'get_message_after_send()'
            )
        );
        Valid::turn();

    }

}
