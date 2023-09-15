<?php
namespace Module\Message;

use Corelib\Func;
use Corelib\Method;
use Make\Database\Pdosql;

//
// Module Controller
// ( View )
//
class View extends \Controller\Make_Controller {

    public function init()
    {
        $this->layout()->view(MOD_MESSAGE_THEME_PATH.'/view.tpl.php');
    }

    public function make()
    {
        $sql = new Pdosql();

        $req = Method::request('get', 'refmode, hash, page');

        Func::getlogin(SET_NOAUTH_MSG);

        // 메시지 본문
        $sql->query(
            "
            select message.*,
            fmember.mb_name as f_mb_name, fmember.mb_id as f_mb_id,
            tmember.mb_name as t_mb_name, tmember.mb_id as t_mb_id
            from {$sql->table("mod:message")} as message
            left outer join
            {$sql->table("member")} as fmember
            on message.from_mb_idx=fmember.mb_idx
            left outer join
            {$sql->table("member")} as tmember
            on message.to_mb_idx=tmember.mb_idx
            where message.hash=:col1 and (message.to_mb_idx=:col2 or message.from_mb_idx=:col2)
            order by message.regdate desc
            ",
            array(
                $req['hash'], MB_IDX
            )
        );

        if ($sql->getcount() < 1) Func::err_back('메시지가 존재하지 않습니다.');

        $arr = $sql->fetchs();

        $arr['regdate'] = Func::datetime($arr['regdate']);
        $arr[0]['list-link'] = Func::get_param_combine('mode='.$req['refmode'].'&page='.$req['page'], '?');

        // 메시지 읽음 처리
        $chked_date = date('Y.m.d H:i:s');

        if (!$arr['chked'] && $arr['to_mb_idx'] == MB_IDX) {
            $sql->query(
                "
                update {$sql->table("mod:message")}
                set chked=:col1
                where hash=:col2 and to_mb_idx=:col3
                ",
                array(
                    $chked_date, $req['hash'], MB_IDX
                )
            );
            $arr['chked'] = $chked_date;

        } else {
            $arr['chked'] = Func::datetime($arr['chked']);

        }

        // 메시지 history
        $sql->query(
            "
            select message.*, member.mb_name, member.mb_id
            from {$sql->table("mod:message")} as message
            left outer join
            {$sql->table("member")} as member
            on message.from_mb_idx=member.mb_idx
            where message.parent_idx=:col1 and message.regdate<:col2 and message.hash!=:col3
            order by message.regdate desc
            ",
            array(
                $arr['parent_idx'], $arr['regdate'], $arr['hash']
            )
        );

        $history_arr = array();

        if ($sql->getcount() > 0) {
            do {
                $hisarr = $sql->fetchs();
                $hisarr['regdate'] = Func::datetime($hisarr['regdate']);

                $history_arr[] = $hisarr;

            } while($sql->nextRec());
        }

        $this->set('view', $arr);
        $this->set('history_arr', $history_arr);
        $this->set('from_mb_id', $arr['f_mb_id']);
        $this->set('reply_parent_idx', $arr['parent_idx']);
        $this->set('refmode', $req['refmode']);
    }

    public function message_tab()
    {
        $fetch = new \Controller\Make_View_Fetch();
        $fetch->set('doc', MOD_MESSAGE_PATH.'/controller/message.tab.inc.php');
        $fetch->set('className', 'Module\Message\message_tab_inc');
        $fetch->run();
    }

}
