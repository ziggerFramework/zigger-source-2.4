<?php
namespace Module\Message;

use Corelib\Func;
use Make\Database\Pdosql;

//
// Module : Message Library
//

class Library {

    // 모듈 초기화
    public function __construct()
    {
        $sql = new Pdosql();

        // update check (1.1.0) : hash 필드 검사, hash 값 강제 삽입
        $sql->query("show columns from {$sql->table("mod:message")} like 'hash'", []);
        if ($sql->getcount() < 1) $sql->query("alter table {$sql->table("mod:message")} add column hash varchar(255) default null", []);
        $sql->query("update {$sql->table("mod:message")} set hash=concat('".Func::make_random_char()."', idx) where (from_mb_idx=:col1 or to_mb_idx=:col1) and (hash is null or hash='')", array(MB_IDX));
    }

    // 새로운 메시지 카운팅
    public function get_new_count()
    { 
        $sql = new Pdosql();

        $total_count = 0;
        if (IS_MEMBER) {
            $sql->query(
                "
                select count(*) as total
                from {$sql->table("mod:message")}
                where to_mb_idx=:col1 and chked is null
                ",
                array(
                    MB_IDX
                )
            );
            $total_count = $sql->fetch('total');

        }

        return $total_count;

    }

}
