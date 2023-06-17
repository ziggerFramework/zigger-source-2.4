<?php
namespace Module\Message;

use Make\Database\Pdosql;

//
// Module : Message Library
//

class Library {

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
