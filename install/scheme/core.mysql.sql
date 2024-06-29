<?php
$SCHEME_CORE = "

create table if not exists `{$req['pfx']}banner` (
    `idx` int(11) not null auto_increment,
    `bn_key` varchar(255) not null,
    `pc_img` text,
    `mo_img` text,
    `title` varchar(255) not null,
    `link` text not null,
    `link_target` varchar(255) default null,
    `hit` int(11) not null default '0',
    `zindex` int(11) not null default '1',
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}banner` add index(`regdate`);

insert into `{$req['pfx']}banner` (`idx`, `bn_key`, `pc_img`, `mo_img`, `title`, `link`, `link_target`, `hit`, `zindex`, `regdate`) values
(1, 'test_banner', '', '', 'test banner', 'https://www.zigger.net', '_self', 0, 1, now());

create table if not exists `{$req['pfx']}blockmb` (
    `idx` int(11) not null auto_increment,
    `ip` varchar(255) default null,
    `mb_idx` int(11) default null,
    `mb_id` varchar(255) default null,
    `memo` text not null,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}blockmb` add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}mailtpl` (
    `idx` int(11) not null auto_increment,
    `type` varchar(255) default null,
    `title` varchar(255) default null,
    `html` text,
    `system` char(1) not null default 'N',
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mailtpl` add index(`regdate`);

insert into `{$req['pfx']}mailtpl` (`idx`, `type`, `title`, `html`, `system`, `regdate`) values
(1, 'signup', '회원가입(이메일) 인증 자동 발송 메일', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 이메일 인증 안내</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">\r\n<p>안녕하세요?<br />\r\n{{name}} 님.<br />\r\n회원 본인 확인을 위해&nbsp;{{site_title}} 이메일 인증 부탁 드립니다.<br />\r\n<br />\r\n아래 링크를 클릭 하시면 이메일이 인증 완료 됩니다.<br />\r\n감사합니다.<br />\r\n<br />\r\n<span style=\"color:#554bbd;\">{{check_url}}</span></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now()),
(2, 'forgot', '로그인 정보 자동 발송 메일', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 로그인 정보 안내</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">\r\n<p>안녕하세요?<br />\r\n{{name}} 님.<br />\r\n{{site_title}} 회원 로그인 정보를 보내 드립니다.<br />\r\n<br />\r\nUser ID : {{id}}<br />\r\nPassword : {{password}}</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now()),
(3, 'default', '기본 템플릿', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 안내 메일</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">{{article}}</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now());

create table if not exists `{$req['pfx']}mbchk` (
    `chk_idx` int(11) not null auto_increment,
    `mb_idx` int(11) not null,
    `chk_code` text,
    `chk_mode` varchar(255) not null default 'chk',
    `chk_chk` char(1) default 'N',
    `chk_regdate` datetime default null,
    `chk_dregdate` datetime default null,
    primary key (`chk_idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mbchk` add index(`mb_idx`), add index(`chk_regdate`);

create table if not exists `{$req['pfx']}mbpoint` (
    `idx` int(11) not null auto_increment,
    `mb_idx` int(11) not null,
    `p_in` int(11) default null,
    `p_out` int(11) default null,
    `memo` text,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mbpoint` add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}member` (
    `mb_adm` char(1) default 'N',
    `mb_idx` int(11) not null auto_increment,
    `mb_id` varchar(255) not null,
    `mb_email` varchar(255) not null,
    `mb_pwd` text not null,
    `mb_name` varchar(255) default null,
    `mb_level` int(11) default '9',
    `mb_gender` char(1) default 'M',
    `mb_phone` varchar(255) default null,
    `mb_telephone` varchar(255) default null,
    `mb_address` text default null,
    `mb_lately` datetime default null,
    `mb_lately_ip` varchar(255) default null,
    `mb_point` int(11) default '0',
    `mb_profileimg` text default null,
    `mb_email_chk` char(1) default 'N',
    `mb_email_chg` varchar(255) default null,
    `mb_sns_ka` text default null,
    `mb_sns_ka_token` text default null,
    `mb_sns_nv` text default null,
    `mb_sns_nv_token` text default null,
    `mb_app_key` text default null,
    `mb_regdate` datetime default null,
    `mb_dregdate` datetime default null,
    `mb_1` text,
    `mb_2` text,
    `mb_3` text,
    `mb_4` text,
    `mb_5` text,
    `mb_6` text,
    `mb_7` text,
    `mb_8` text,
    `mb_9` text,
    `mb_10` text,
    `mb_exp` text not null,
    primary key (`mb_idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}member` add index(`mb_idx`), add index(`mb_regdate`), add index(`mb_dregdate`);

create table if not exists `{$req['pfx']}mng_feeds` (
    `idx` int(11) not null auto_increment,
    `msg_from` text,
    `href` text,
    `memo` text,
    `regdate` datetime default null,
    `chked` char(1) default 'N',
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}mng_feeds` add index(`regdate`);

create table if not exists `{$req['pfx']}popup` (
    `idx` int(11) not null auto_increment,
    `id` varchar(255) not null,
    `title` varchar(255) not null,
    `link` text,
    `link_target` varchar(255) default null,
    `width` int(11) default null,
    `height` int(11) default null,
    `pos_left` int(11) default null,
    `pos_top` int(11) default null,
    `level_from` int(11) default null,
    `level_to` int(11) default null,
    `show_from` datetime default null,
    `show_to` datetime default null,
    `html` text,
    `mo_html` text,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}popup` add index(`regdate`);

create table if not exists `{$req['pfx']}sentmail` (
    `idx` int(11) not null auto_increment,
    `template` varchar(255) default null,
    `to_mb` varchar(255) default null,
    `level_from` int(11) default null,
    `level_to` int(11) default null,
    `to_count` int(11) default '0',
    `subject` text,
    `html` text,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}sentmail` add index(`regdate`);

create table if not exists `{$req['pfx']}sentsms` (
    `idx` int(11) not null auto_increment,
    `sendtype` varchar(255) default null,
    `to_mb` varchar(255) default null,
    `to_phone` text default null,
    `level_from` int(11) default null,
    `level_to` int(11) default null,
    `to_count` int(11) default '0',
    `subject` text default null,
    `memo` text default null,
    `use_resv` text default null,
    `resv_date` text default null,
    `resv_hour` text default null,
    `resv_min` text default null,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}sentsms` add index(`regdate`);

create table if not exists `{$req['pfx']}session` (
    `idx` int(11) not null auto_increment,
    `sesskey` text not null,
    `expiry` int(11) not null,
    `value` text,
    `mb_idx` int(11) default '0',
    `ip` varchar(255) default null,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}session` add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}config` (
    `cfg_idx` int(11) not null auto_increment,
    `cfg_type` text not null,
    `cfg_key` text not null,
    `cfg_value` text default null,
    `cfg_regdate` datetime default null,
    primary key (`cfg_idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}config` add index(`cfg_regdate`);

insert into `{$req['pfx']}config` (`cfg_type`, `cfg_key`, `cfg_value`, `cfg_regdate`) values
('engine', 'title', 'zigger Website', now()),
('engine', 'domain', '{$protocol}{$_SERVER['HTTP_HOST']}{$realdir}', now()),
('engine', 'description', 'zigger Website Description', now()),
('engine', 'use_mobile', 'Y', now()),
('engine', 'use_emailchk', 'Y', now()),
('engine', 'use_recaptcha', '', now()),
('engine', 'recaptcha_key1', '', now()),
('engine', 'recaptcha_key2', '', now()),
('engine', 'email', '', now()),
('engine', 'tel', '', now()),
('engine', 'favicon', '', now()),
('engine', 'logo', '', now()),
('engine', 'mb_division', '최고관리자|관리자|게시판관리자|정회원|정회원|정회원|정회원|정회원|일반회원|비회원', now()),
('engine', 'og_type', 'website', now()),
('engine', 'og_title', 'zigger Website', now()),
('engine', 'og_description', 'zigger Website Description', now()),
('engine', 'og_image', '', now()),
('engine', 'og_url', '{$protocol}{$_SERVER['HTTP_HOST']}{$realdir}', now()),
('engine', 'naver_verific', '', now()),
('engine', 'google_verific', '', now()),
('engine', 'theme', 'zigger-basic', now()),
('engine', 'use_smtp', 'N', now()),
('engine', 'smtp_server', '', now()),
('engine', 'smtp_port', '', now()),
('engine', 'smtp_id', '', now()),
('engine', 'smtp_pwd', '', now()),
('engine', 'script', '', now()),
('engine', 'meta', '', now()),
('engine', 'privacy', '개인정보 처리방침은 Manager에서 관리할 수 있습니다.', now()),
('engine', 'policy', '이용약관은 Manager에서 관리할 수 있습니다.', now()),
('engine', 'use_sns_ka', 'N', now()),
('engine', 'sns_ka_key1', '', now()),
('engine', 'sns_ka_key2', '', now()),
('engine', 'use_sns_nv', 'N', now()),
('engine', 'sns_nv_key1', '', now()),
('engine', 'sns_nv_key2', '', now()),
('engine', 'use_s3', 'N', now()),
('engine', 's3_key1', '', now()),
('engine', 's3_key2', '', now()),
('engine', 's3_key3', '', now()),
('engine', 's3_key4', '', now()),
('engine', 's3_key5', '', now()),
('engine', 'use_sms', 'N', now()),
('engine', 'use_feedsms', 'N', now()),
('engine', 'sms_toadm', '', now()),
('engine', 'sms_from', '', now()),
('engine', 'sms_key1', '', now()),
('engine', 'sms_key2', '', now()),
('engine', 'sms_key3', '', now()),
('engine', 'sms_key4', '', now()),
('engine', 'use_mb_phone', 'N', now()),
('engine', 'use_phonechk', 'N', now()),
('engine', 'use_mb_telephone', 'N', now()),
('engine', 'use_mb_address', 'N', now()),
('engine', 'use_mb_gender', 'N', now()),
('engine', 'use_rss', 'N', now()),
('engine', 'rss_boards', '{\r\n \"rss\" : [\r\n  {\r\n   \"board_id\" : \"news\",\r\n   \"title\" : \"News\",\r\n   \"link\" : \"{$protocol}{$_SERVER['HTTP_HOST']}{$realdir}/sub/board/news\"\r\n  },\r\n  {\r\n   \"board_id\" : \"freeboard\",\r\n   \"title\" : \"Freeboard\",\r\n   \"link\" : \"{$protocol}{$_SERVER['HTTP_HOST']}{$realdir}/sub/board/free\"\r\n  }\r\n ]\r\n}', now()),
('engine', 'st_1', '', now()),
('engine', 'st_2', '', now()),
('engine', 'st_3', '', now()),
('engine', 'st_4', '', now()),
('engine', 'st_5', '', now()),
('engine', 'st_6', '', now()),
('engine', 'st_7', '', now()),
('engine', 'st_8', '', now()),
('engine', 'st_9', '', now()),
('engine', 'st_10', '', now()),
('engine', 'st_exp', '|||||||||', now());

create table if not exists `{$req['pfx']}sitemap` (
    `idx` int(11) not null,
    `caidx` text,
    `title` varchar(255) default null,
    `href` text,
    `visible` char(1) not null default 'Y',
    `children` int(11) not null default '0',
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}sitemap`;

insert into `{$req['pfx']}sitemap` (`idx`, `caidx`, `title`, `href`, `visible`, `children`) values
(1, '0001', 'Introduce', 'sub/view/contents', 'Y', 2),
(2, '00010001', 'zigger 소개', 'sub/view/contents', 'Y', 0),
(3, '00010002', 'Manager 소개', 'sub/view/manager', 'Y', 0),
(4, '0002', 'Community', 'sub/board/news', 'Y', 2),
(5, '00020001', 'News', 'sub/board/news', 'Y', 0),
(6, '00020002', 'Freeboard', 'sub/board/free', 'Y', 0),
(7, '0003', 'Contact', 'sub/view/contactus', 'Y', 0);

create table if not exists `{$req['pfx']}visitcount` (
    `idx` int(11) not null auto_increment,
    `mb_idx` int(11) default null,
    `mb_id` varchar(255) default null,
    `ip` varchar(20) default null,
    `device` varchar(255) default null,
    `browser` varchar(255) default null,
    `regdate` datetime default null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}visitcount` add unique key `fkey1` (`ip`,`regdate`), add index(`mb_idx`), add index(`regdate`);

create table if not exists `{$req['pfx']}dataupload` (
    `idx` int(11) not null auto_increment,
    `filepath` text default null,
    `orgfile` text default null,
    `repfile` text default null,
    `byte` int(11) default null,
    `storage` char(1) not null default 'N',
    `regdate` datetime not null,
    primary key (`idx`)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

alter table `{$req['pfx']}dataupload` add index(`regdate`);

";
