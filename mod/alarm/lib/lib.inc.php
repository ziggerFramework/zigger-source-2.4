<?php
namespace Module\Alarm;

use Corelib\Func;
use Make\Database\Pdosql;

//
// Module : Alarm Library
//

class Library {

    // 모듈 초기화
    public function __construct()
    {
        $sql = new Pdosql();

        // update check (1.1.0) : hash 필드 검사, hash 값 강제 삽입
        $sql->query("show columns from {$sql->table("mod:alarm")} like 'hash'", []);
        if ($sql->getcount() < 1) $sql->query("alter table {$sql->table("mod:alarm")} add column hash varchar(255) default null", []);
        $sql->query("update {$sql->table("mod:alarm")} set hash=concat('".Func::make_random_char()."', idx) where (from_mb_idx=:col1 or to_mb_idx=:col1) and (hash is null or hash='')", array(MB_IDX));
    }

    // 새로운 알림 카운팅
    public function get_new_count()
    {
        $sql = new Pdosql();

        $total_count = 0;
        if (IS_MEMBER) {
            $sql->query(
                "
                select count(*) as total
                from {$sql->table("mod:alarm")}
                where to_mb_idx=:col1 and chked='N'
                ",
                array(
                    MB_IDX
                )
            );
            $total_count = $sql->fetch('total');

        }

        return $total_count;

    }

    // 새로운 알림 등록
    public function get_add_alarm($arr)
    {
        $sql = new Pdosql();

        if (!isset($arr['to_mb_idx']) || !$arr['to_mb_idx']) return false;

        $qry = array();

        // 수신자가 다수인 경우
        if (is_array($arr['to_mb_idx'])) {
            foreach ($arr['to_mb_idx'] as $key => $value) {
                $qry[] = "('".addslashes(Func::make_random_char())."', '".addslashes($arr['msg_from'])."', '".addslashes($arr['from_mb_idx'])."', '".addslashes($value)."', '".addslashes($arr['link'])."', '".addslashes($arr['memo'])."', now())";
            }
        }
        
        // 수신자가 한명인 경우
        else {
            $qry [] = "('".addslashes(Func::make_random_char())."', '".addslashes($arr['msg_from'])."', '".addslashes($arr['from_mb_idx'])."', '".addslashes($arr['to_mb_idx'])."', '".addslashes($arr['link'])."', '".addslashes($arr['memo'])."', now())";
        }

        $sql->query(
            "
            insert into
            {$sql->table("mod:alarm")}
            (hash, msg_from, from_mb_idx, to_mb_idx, href, memo, regdate)
            values
            ".implode(',', $qry), []
        );
    }

}
