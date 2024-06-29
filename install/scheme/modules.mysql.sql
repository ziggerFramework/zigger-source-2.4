<?php
$SCHEME_MOD = "

create table `{$req['pfx']}mod_board_temporary` (
  `idx` int(11) unsigned not null auto_increment,
  `id` varchar(255),
  `mb_idx` int(11) default '0',
  `hash` varchar(255) default '',
  `subject` varchar(255) character set utf8mb4 collate utf8mb4_unicode_ci,
  `article` text character set utf8mb4 collate utf8mb4_unicode_ci,
  `regdate` datetime default null,
  primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_temporary` add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_board_data_freeboard` (
    `idx` int(11) not null auto_increment,
    `category` varchar(255) default null,
    `ln` int(11) default '0',
    `rn` int(11) default '0',
    `mb_idx` int(11) default '0',
    `mb_id` varchar(255) default null,
    `writer` varchar(255) default null,
    `pwd` text,
    `email` varchar(255) default null,
    `article` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `subject` varchar(255) character set utf8mb4 collate utf8mb4_unicode_ci default null,
    `file1` text,
    `file1_cnt` int(11) default '0',
    `file2` text,
    `file2_cnt` int(11) default '0',
    `use_secret` char(1) default 'N',
    `use_notice` char(1) default 'N',
    `use_html` char(1) default 'Y',
    `use_email` char(1) default 'Y',
    `view` int(11) default '0',
    `ip` varchar(255) default null,
    `regdate` datetime default null,
    `dregdate` datetime default null,
    `data_1` text,
    `data_2` text,
    `data_3` text,
    `data_4` text,
    `data_5` text,
    `data_6` text,
    `data_7` text,
    `data_8` text,
    `data_9` text,
    `data_10` text,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_data_freeboard` add index(`ln`), add index(`rn`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_board_cmt_freeboard` (
    `idx` int(11) not null auto_increment,
    `ln` int(11) default '0',
    `rn` int(11) default '0',
    `bo_idx` int(11) default null,
    `mb_idx` int(11) default '0',
    `writer` varchar(255) default null,
    `parent_mb_idx` int(11) default '0',
    `parent_writer` varchar(255) default null,
    `comment` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `ip` varchar(255) default null,
    `regdate` datetime default null,
    `cmt_1` text,
    `cmt_2` text,
    `cmt_3` text,
    `cmt_4` text,
    `cmt_5` text,
    `cmt_6` text,
    `cmt_7` text,
    `cmt_8` text,
    `cmt_9` text,
    `cmt_10` text,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_cmt_freeboard` add index(`bo_idx`), add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_board_data_news` (
    `idx` int(11) not null auto_increment,
    `category` varchar(255) default null,
    `ln` int(11) default '0',
    `rn` int(11) default '0',
    `mb_idx` int(11) default '0',
    `mb_id` varchar(255) default null,
    `writer` varchar(255) default null,
    `pwd` text,
    `email` varchar(255) default null,
    `article` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `subject` varchar(255) character set utf8mb4 collate utf8mb4_unicode_ci default null,
    `file1` text,
    `file1_cnt` int(11) default '0',
    `file2` text,
    `file2_cnt` int(11) default '0',
    `use_secret` char(1) default 'N',
    `use_notice` char(1) default 'N',
    `use_html` char(1) default 'Y',
    `use_email` char(1) default 'Y',
    `view` int(11) default '0',
    `ip` varchar(255) default null,
    `regdate` datetime default null,
    `dregdate` datetime default null,
    `data_1` text,
    `data_2` text,
    `data_3` text,
    `data_4` text,
    `data_5` text,
    `data_6` text,
    `data_7` text,
    `data_8` text,
    `data_9` text,
    `data_10` text,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_data_news` add index(`ln`), add index(`rn`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_board_cmt_news` (
    `idx` int(11) not null auto_increment,
    `ln` int(11) default '0',
    `rn` int(11) default '0',
    `bo_idx` int(11) default null,
    `mb_idx` int(11) default '0',
    `writer` varchar(255) default null,
    `parent_mb_idx` int(11) default '0',
    `parent_writer` varchar(255) default null,
    `comment` text,
    `ip` varchar(255) default null,
    `regdate` datetime default null,
    `cmt_1` text,
    `cmt_2` text,
    `cmt_3` text,
    `cmt_4` text,
    `cmt_5` text,
    `cmt_6` text,
    `cmt_7` text,
    `cmt_8` text,
    `cmt_9` text,
    `cmt_10` text,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_cmt_news` add index(`bo_idx`), add index(`mb_idx`), add index(`regdate`);

insert into `{$req['pfx']}config` (`cfg_type`, `cfg_key`, `cfg_value`, `cfg_regdate`) values
('mod:board:config:freeboard', 'id', 'freeboard', now()),
('mod:board:config:freeboard', 'theme', 'gallery', now()),
('mod:board:config:freeboard', 'title', 'Freeboard', now()),
('mod:board:config:freeboard', 'use_list', 'Y|Y', now()),
('mod:board:config:freeboard', 'use_secret', 'Y', now()),
('mod:board:config:freeboard', 'use_seek', 'Y', now()),
('mod:board:config:freeboard', 'use_comment', 'Y', now()),
('mod:board:config:freeboard', 'use_likes', 'Y', now()),
('mod:board:config:freeboard', 'use_reply', 'Y', now()),
('mod:board:config:freeboard', 'use_file1', 'Y', now()),
('mod:board:config:freeboard', 'use_file2', 'Y', now()),
('mod:board:config:freeboard', 'use_mng_feed', 'Y', now()),
('mod:board:config:freeboard', 'use_category', 'N', now()),
('mod:board:config:freeboard', 'category', '', now()),
('mod:board:config:freeboard', 'file_limit', '5242880', now()),
('mod:board:config:freeboard', 'list_limit', '15|10', now()),
('mod:board:config:freeboard', 'sbj_limit', '50|30', now()),
('mod:board:config:freeboard', 'txt_limit', '150|100', now()),
('mod:board:config:freeboard', 'article_min_len', '30', now()),
('mod:board:config:freeboard', 'list_level', '10', now()),
('mod:board:config:freeboard', 'write_level', '10', now()),
('mod:board:config:freeboard', 'secret_level', '1', now()),
('mod:board:config:freeboard', 'comment_level', '10', now()),
('mod:board:config:freeboard', 'delete_level', '10', now()),
('mod:board:config:freeboard', 'read_level', '10', now()),
('mod:board:config:freeboard', 'ctr_level', '3', now()),
('mod:board:config:freeboard', 'reply_level', '10', now()),
('mod:board:config:freeboard', 'write_point', '10', now()),
('mod:board:config:freeboard', 'read_point', '0', now()),
('mod:board:config:freeboard', 'top_source', '', now()),
('mod:board:config:freeboard', 'bottom_source', '', now()),
('mod:board:config:freeboard', 'ico_file', 'Y', now()),
('mod:board:config:freeboard', 'ico_secret', 'Y', now()),
('mod:board:config:freeboard', 'ico_secret_def', 'N', now()),
('mod:board:config:freeboard', 'ico_new', 'Y', now()),
('mod:board:config:freeboard', 'ico_new_case', '4320', now()),
('mod:board:config:freeboard', 'ico_hot', 'Y', now()),
('mod:board:config:freeboard', 'ico_hot_case', '10|AND|50', now()),
('mod:board:config:freeboard', 'conf_1', '', now()),
('mod:board:config:freeboard', 'conf_2', '', now()),
('mod:board:config:freeboard', 'conf_3', '', now()),
('mod:board:config:freeboard', 'conf_4', '', now()),
('mod:board:config:freeboard', 'conf_5', '', now()),
('mod:board:config:freeboard', 'conf_6', '', now()),
('mod:board:config:freeboard', 'conf_7', '', now()),
('mod:board:config:freeboard', 'conf_8', '', now()),
('mod:board:config:freeboard', 'conf_9', '', now()),
('mod:board:config:freeboard', 'conf_10', '', now()),
('mod:board:config:freeboard', 'conf_exp', '|||||||||', now()),
('mod:board:config:news', 'id', 'news', now()),
('mod:board:config:news', 'theme', 'basic', now()),
('mod:board:config:news', 'title', 'News', now()),
('mod:board:config:news', 'use_list', 'Y|Y', now()),
('mod:board:config:news', 'use_secret', 'Y', now()),
('mod:board:config:news', 'use_seek', 'Y', now()),
('mod:board:config:news', 'use_comment', 'Y', now()),
('mod:board:config:news', 'use_likes', 'Y', now()),
('mod:board:config:news', 'use_reply', 'Y', now()),
('mod:board:config:news', 'use_file1', 'Y', now()),
('mod:board:config:news', 'use_file2', 'Y', now()),
('mod:board:config:news', 'use_mng_feed', 'Y', now()),
('mod:board:config:news', 'use_category', 'N', now()),
('mod:board:config:news', 'category', '', now()),
('mod:board:config:news', 'file_limit', '5242880', now()),
('mod:board:config:news', 'list_limit', '15|10', now()),
('mod:board:config:news', 'sbj_limit', '50|30', now()),
('mod:board:config:news', 'txt_limit', '150|100', now()),
('mod:board:config:news', 'article_min_len', '30', now()),
('mod:board:config:news', 'list_level', '10', now()),
('mod:board:config:news', 'write_level', '10', now()),
('mod:board:config:news', 'secret_level', '1', now()),
('mod:board:config:news', 'comment_level', '10', now()),
('mod:board:config:news', 'delete_level', '10', now()),
('mod:board:config:news', 'read_level', '10', now()),
('mod:board:config:news', 'ctr_level', '3', now()),
('mod:board:config:news', 'reply_level', '10', now()),
('mod:board:config:news', 'write_point', '10', now()),
('mod:board:config:news', 'read_point', '0', now()),
('mod:board:config:news', 'top_source', '', now()),
('mod:board:config:news', 'bottom_source', '', now()),
('mod:board:config:news', 'ico_file', 'Y', now()),
('mod:board:config:news', 'ico_secret', 'Y', now()),
('mod:board:config:news', 'ico_secret_def', 'N', now()),
('mod:board:config:news', 'ico_new', 'Y', now()),
('mod:board:config:news', 'ico_new_case', '4320', now()),
('mod:board:config:news', 'ico_hot', 'Y', now()),
('mod:board:config:news', 'ico_hot_case', '10|AND|50', now()),
('mod:board:config:news', 'conf_1', '', now()),
('mod:board:config:news', 'conf_2', '', now()),
('mod:board:config:news', 'conf_3', '', now()),
('mod:board:config:news', 'conf_4', '', now()),
('mod:board:config:news', 'conf_5', '', now()),
('mod:board:config:news', 'conf_6', '', now()),
('mod:board:config:news', 'conf_7', '', now()),
('mod:board:config:news', 'conf_8', '', now()),
('mod:board:config:news', 'conf_9', '', now()),
('mod:board:config:news', 'conf_10', '', now()),
('mod:board:config:news', 'conf_exp', '|||||||||', now());

create table if not exists `{$req['pfx']}mod_board_like` (
    `idx` int(11) not null auto_increment,
    `id` varchar(255) default null,
    `data_idx` int(11) default null,
    `mb_idx` int(11) default null,
    `likes` int(11) default '0',
    `unlikes` int(11) default '0',
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_board_like` add index(`data_idx`), add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_contactform` (
    `idx` int(11) not null auto_increment,
    `rep_idx` int(11) default '0',
    `mb_idx` int(11) default '0',
    `article` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `name` varchar(255) default null,
    `email` text,
    `phone` varchar(255) default null,
    `regdate` datetime default null,
    `contact_1` text,
    `contact_2` text,
    `contact_3` text,
    `contact_4` text,
    `contact_5` text,
    `contact_6` text,
    `contact_7` text,
    `contact_8` text,
    `contact_9` text,
    `contact_10` text,
    `contact_exp` text,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_contactform` add index(`rep_idx`), add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_contents` (
    `idx` int(11) not null auto_increment,
    `data_key` varchar(255) not null,
    `title` varchar(255) default null,
    `html` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `mo_html` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `use_mo_html` char(1) not null default 'N',
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

insert into `{$req['pfx']}mod_contents` (`idx`, `data_key`, `title`, `html`, `mo_html`, `use_mo_html`, `regdate`) values
(1, 'sample', 'About zigger', '<p><span style=\"font-size:38px;letter-spacing:-2px;color:#333\"><img src=\"{$realdir}/theme/zigger-basic/layout/images/logo.png\" style=\"width: 110px; height: 30px;\" />&nbsp;를 선택해&nbsp;주셔서 감사합니다!</span></p>\r\n\r\n<div style=\"margin:30px 0;border-radius:10px;border:1px solid #ddd;padding:45px;background:#f7f7f7;line-height:30px;letter-spacing:-1px;\"><span style=\"color:#666666;letter-spacing:-1px;\"><span style=\"font-size:15px;letter-spacing:-1px;\">zigger는 MVC PHP로 개발된 CMS Framework 입니다.<br />\r\nMVC 로직을 통해 빠르게 반응형 웹사이트를 구축할 수 있으며,<br />\r\nzigger 공식 사이트에서 배포하는 다양한 모듈을 Core에 추가하여 원하는 기능을 쉽고 간편하게 구축할 수 있습니다.<br />\r\n<br />\r\n공식 웹사이트(https://www.zigger.net)를 통해 지속적인 업데이트를 지원 받으실&nbsp;수 있으며,<br />\r\n이용 가이드 및 다양한 소식을 빠르게 확인할 수 있습니다.<br />\r\n<br />\r\nzigger는 GNU 라이선스가 적용되어 있어 영리 및 비영리 웹사이트 구축시 자유롭게 사용할 수 있습니다.<br />\r\n다만, 무단 수정 및 배포는 금지되어 있으므로, zigger가 설치된 ROOT 리렉토리 내 LICENSE 파일을 확인 하시길 바랍니다.<br />\r\n<br />\r\n공식 웹사이트는 아래 버튼을 클릭하여 접속 가능합니다.<br />\r\nzigger를 설치해 주셔서 감사합니다.</span></span><br />\r\n<br />\r\n<strong><span style=\"font-size:12px;letter-spacing:-1px;\">이 웹페이지는 콘텐츠 모듈 ( &#39;Manage &gt; 모듈 &gt; 콘텐츠&#39; ) 로 제작된 페이지입니다. Manage에서 본 샘플 웹페이지를 확인해 보세요.</span></strong></div>\r\n\r\n<p><a href=\"https://www.zigger.net/\" style=\"display:block;margin:0 auto;line-height:50px;border-radius:4px;text-align:center;width:260px;padding:0 20px;background:#564bbe;font-size:16px;color:#fff;letter-spacing:-1px;\" target=\"_blank\">zigger 공식 사이트 이동</a></p>\r\n\r\n<p>&nbsp;</p>\r\n', '<p><span style=\"letter-spacing: -1px; font-size:22px;color: rgb(51, 51, 51);\"><img src=\"{$realdir}/theme/zigger-basic/layout/images/logo.png\" style=\"width: 60px; height: 16px;\" />&nbsp;를 선택해&nbsp;주셔서 감사합니다!</span></p>\r\n\r\n<div style=\"margin:20px 0;border-radius:10px;border:1px solid #ddd;padding:15px;background:#f7f7f7;line-height:20px;letter-spacing:-1px;\"><span style=\"font-size:12px;\"><span style=\"color:#666666;letter-spacing:-1px;\"><span style=\"letter-spacing: -1px;\">zigger는 MVC PHP로 개발된 CMS Framework 입니다.<br />\r\nMVC 로직을 통해 빠르게 반응형 웹사이트를 구축할 수 있으며,<br />\r\nzigger 공식 사이트에서 배포하는 다양한 모듈을 Core에 추가하여 원하는 기능을 쉽고 간편하게 구축할 수 있습니다.<br />\r\n<br />\r\n공식 웹사이트(https://www.zigger.net)를 통해 지속적인 업데이트를 지원 받으실&nbsp;수 있으며,<br />\r\n이용 가이드 및 다양한 소식을 빠르게 확인할 수 있습니다.<br />\r\n<br />\r\nzigger는 GNU 라이선스가 적용되어 있어 영리 및 비영리 웹사이트 구축시 자유롭게 사용할 수 있습니다.<br />\r\n다만, 무단 수정 및 배포는 금지되어 있으므로, zigger가 설치된 ROOT 리렉토리 내 LICENSE 파일을 확인 하시길 바랍니다.<br />\r\n<br />\r\n공식 웹사이트는 아래 버튼을 클릭하여 접속 가능합니다.<br />\r\nzigger를 설치해 주셔서 감사합니다.<br />\r\n<br />\r\n<strong><span style=\"font-size:12px;letter-spacing:-1px;\">이 웹페이지는 콘텐츠 모듈 ( &#39;Manage &gt; 모듈 &gt; 콘텐츠&#39; ) 로 제작된 페이지입니다. Manage에서 본 샘플 웹페이지를 확인해 보세요.</span></strong></span></span></span></div>\r\n\r\n<p><a href=\"https://www.zigger.net/\" style=\"display:block;margin:0 auto;line-height:40px;border-radius:4px;text-align:center;width:160px;padding:0 10px;background:#564bbe;font-size:13px;color:#fff;letter-spacing:-1px;\" target=\"_blank\">zigger 공식 사이트 이동</a></p>\r\n\r\n<p>&nbsp;</p>\r\n', 'Y', now());

create table if not exists `{$req['pfx']}mod_message` (
    `idx` int(11) not null auto_increment,
    `hash` varchar(255) default null,
    `from_mb_idx` int(11) not null,
    `to_mb_idx` int(11) not null,
    `parent_idx` int(11) default null,
    `article` text character set utf8mb4 collate utf8mb4_unicode_ci,
    `regdate` datetime not null,
    `chked` datetime default null,
    primary key (idx)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_message` add index(`from_mb_idx`), add index(`to_mb_idx`), add index(`parent_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_alarm` (
    `idx` int(11) not null auto_increment,
    `hash` varchar(255) default null,
    `msg_from` text,
    `from_mb_idx` int(11) default null,
    `to_mb_idx` int(11) not null,
    `href` text,
    `memo` text,
    `regdate` datetime default null,
    `chked` char(1) DEFAULT 'N',
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_alarm` add index(`from_mb_idx`), add index(`to_mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mod_search` (
    `idx` int(11) not null auto_increment,
    `caidx` text DEFAULT null,
    `title` varchar(255) DEFAULT null,
    `opt` text DEFAULT null,
    `href` text DEFAULT null,
    `children` int(11) not null DEFAULT 0,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mod_search` add index(`ca_idx`);

insert into `{$req['pfx']}mod_search` (`idx`, `caidx`, `title`, `opt`, `href`, `children`) values
(1, '0001', 'About zigger', 'contents|sample|1', 'sub/view/contents', 0),
(2, '0002', 'News 게시판', 'board|news|20', 'sub/board/news', 0),
(3, '0003', 'Freeboard 게시판', 'board|freeboard|20', 'sub/board/free', 0);

";
