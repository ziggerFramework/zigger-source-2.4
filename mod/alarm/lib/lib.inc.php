<?php
namespace Module\Alarm;

use Make\Database\Pdosql;

//
// Module : Alarm Library
//

class Library {

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

        $sql->query(
            "
            insert into
            {$sql->table("mod:alarm")}
            (msg_from, from_mb_idx, to_mb_idx, href, memo, regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                $arr['msg_from'], $arr['from_mb_idx'], $arr['to_mb_idx'], $arr['link'], $arr['memo']
            )
        );
    }

}
