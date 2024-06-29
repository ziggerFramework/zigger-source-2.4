<?php
namespace Module\Recentposts;

use Make\Database\Pdosql;

class Installation {

    // 모듈 설치 여부 검사
    static public function check_installed_module()
    {
        $sql = new Pdosql();
        if (!$sql->table_exists('mod:recentposts')) return false;
        return true;
    }

    // 모듈 자동 설치
    static public function get_auto_install_module()
    {
        global $sql;
        
        $sql = new Pdosql();

        $sql->query(
            "
            insert into
            {$sql->table('config')}
            (`cfg_type`, `cfg_key`, `cfg_value`, `cfg_regdate`)
            values
            ('mod:recentposts:config', 'boards', '', now());
            ", []
        );

        $sql->query(
            "
            create table if not exists `{$sql->table('mod:recentposts')}` (
                `idx` int(11) not null auto_increment,
                `board_id` varchar(255) not null,
                `bo_idx` int(11) not null,
                `mb_idx` int(11) not null,
                `mb_id` varchar(255) default null,
                `writer` varchar(255) default null,
                `subject` text character set utf8mb4 collate utf8mb4_unicode_ci default null,
                `article` text character set utf8mb4 collate utf8mb4_unicode_ci default null,
                `view` int(11) default 0,
                `file1` text default null,
                `file2` text default null,
                `regdate` datetime default null,
                primary key (`idx`)
            ) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;
            ", []
        );

        $sql->query(
            "
            alter table `{$sql->table('mod:recentposts')}` add index(`regdate`);
            ", []
        );
    }

}
