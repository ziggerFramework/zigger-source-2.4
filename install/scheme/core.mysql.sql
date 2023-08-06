<?php
$SCHEME_CORE = "

CREATE TABLE IF NOT EXISTS `{$req['pfx']}banner` (
    `idx` int(11) NOT NULL auto_increment,
    `bn_key` varchar(255) NOT NULL,
    `pc_img` text,
    `mo_img` text,
    `title` varchar(255) NOT NULL,
    `link` text NOT NULL,
    `link_target` varchar(255) default NULL,
    `hit` int(11) NOT NULL default '0',
    `zindex` int(11) NOT NULL default '1',
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$req['pfx']}banner` (`idx`, `bn_key`, `pc_img`, `mo_img`, `title`, `link`, `link_target`, `hit`, `zindex`, `regdate`) VALUES
(1, 'test_banner', '', '', 'test banner', 'https://www.zigger.net', '_self', 0, 1, now());

CREATE TABLE IF NOT EXISTS `{$req['pfx']}blockmb` (
    `idx` int(11) NOT NULL auto_increment,
    `ip` varchar(255) default NULL,
    `mb_idx` int(11) default NULL,
    `mb_id` varchar(255) default NULL,
    `memo` text NOT NULL,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `{$req['pfx']}blockmb` ADD INDEX(`mb_idx`);

CREATE TABLE IF NOT EXISTS `{$req['pfx']}mailtpl` (
    `idx` int(11) NOT NULL auto_increment,
    `type` varchar(255) BINARY default NULL,
    `title` varchar(255) default NULL,
    `html` text,
    `system` char(1) NOT NULL default 'N',
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{$req['pfx']}mailtpl` (`idx`, `type`, `title`, `html`, `system`, `regdate`) VALUES
(1, 'signup', '회원가입(이메일) 인증 자동 발송 메일', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 이메일 인증 안내</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">\r\n<p>안녕하세요?<br />\r\n{{name}} 님.<br />\r\n회원 본인 확인을 위해&nbsp;{{site_title}} 이메일 인증 부탁 드립니다.<br />\r\n<br />\r\n아래 링크를 클릭 하시면 이메일이 인증 완료 됩니다.<br />\r\n감사합니다.<br />\r\n<br />\r\n<span style=\"color:#554bbd;\">{{check_url}}</span></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now()),
(2, 'forgot', '로그인 정보 자동 발송 메일', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 로그인 정보 안내</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">\r\n<p>안녕하세요?<br />\r\n{{name}} 님.<br />\r\n{{site_title}} 회원 로그인 정보를 보내 드립니다.<br />\r\n<br />\r\nUser ID : {{id}}<br />\r\nPassword : {{password}}</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now()),
(3, 'default', '기본 템플릿', '<table align=\"center\" style=\"width:740px;border-collapse: collapse;background:#fff;\">\r\n	<tbody>\r\n<tr>\r\n<td style=\"padding:20px 40px;border-bottom:2px solid #ddd;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;\"><strong>{{site_title}}</strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:40px 40px 20px 40px;font-size: 36px;color: #554bbd;letter-spacing:-3px;font-family:Malgun Gothic;\">{{site_title}} 안내 메일</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding:0 40px 40px 40px;font-size:16px;line-height:30px;color:#666666;font-family:Malgun Gothic;letter-spacing:-1px;border-bottom:1px solid #ddd;\">{{article}}</td>\r\n</tr>\r\n<tr>\r\n<td style=\"border:1px solid #ddd;border-width:1px 0 1px 0;padding:20px 15px;text-align:center;font-size:12px;line-height:20px;font-family:dotum;color:#999;\">ⓒ {{site_title}} All Rights Reserved.</td>\r\n</tr>\r\n	</tbody>\r\n</table>\r\n', 'Y', now());

CREATE TABLE IF NOT EXISTS `{$req['pfx']}mbchk` (
    `chk_idx` int(11) NOT NULL auto_increment,
    `mb_idx` int(11) NOT NULL,
    `chk_code` text,
    `chk_mode` varchar(255) NOT NULL default 'chk',
    `chk_chk` char(1) default 'N',
    `chk_regdate` datetime default NULL,
    `chk_dregdate` datetime default NULL,
    PRIMARY KEY  (`chk_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `{$req['pfx']}mbchk` ADD INDEX(`mb_idx`);

CREATE TABLE IF NOT EXISTS `{$req['pfx']}mbpoint` (
    `idx` int(11) NOT NULL auto_increment,
    `mb_idx` int(11) NOT NULL,
    `p_in` int(11) default NULL,
    `p_out` int(11) default NULL,
    `memo` text,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `{$req['pfx']}mbpoint` ADD INDEX(`mb_idx`);

CREATE TABLE IF NOT EXISTS `{$req['pfx']}member` (
    `mb_adm` char(1) default 'N',
    `mb_idx` int(11) NOT NULL auto_increment,
    `mb_id` varchar(255) NOT NULL,
    `mb_email` varchar(255) NOT NULL,
    `mb_pwd` text NOT NULL,
    `mb_name` varchar(255) default NULL,
    `mb_level` int(11) default '9',
    `mb_gender` char(1) default 'M',
    `mb_phone` varchar(255) default NULL,
    `mb_telephone` varchar(255) default NULL,
    `mb_address` text default NULL,
    `mb_lately` datetime default NULL,
    `mb_lately_ip` varchar(255) default NULL,
    `mb_point` int(11) default '0',
    `mb_profileimg` text default NULL,
    `mb_email_chk` char(1) default 'N',
    `mb_email_chg` varchar(255) default NULL,
    `mb_sns_ka` text default NULL,
    `mb_sns_ka_token` text default NULL,
    `mb_sns_nv` text default NULL,
    `mb_sns_nv_token` text default NULL,
    `mb_app_key` text default NULL,
    `mb_regdate` datetime default NULL,
    `mb_dregdate` datetime default NULL,
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
    `mb_exp` text NOT NULL,
    PRIMARY KEY  (`mb_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$req['pfx']}mng_feeds` (
    `idx` int(11) NOT NULL auto_increment,
    `msg_from` text collate utf8_bin,
    `href` text collate utf8_bin,
    `memo` text collate utf8_bin,
    `regdate` datetime default NULL,
    `chked` char(1) collate utf8_bin default 'N',
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$req['pfx']}popup` (
    `idx` int(11) NOT NULL auto_increment,
    `id` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `link` text,
    `link_target` varchar(255) default NULL,
    `width` int(11) default NULL,
    `height` int(11) default NULL,
    `pos_left` int(11) default NULL,
    `pos_top` int(11) default NULL,
    `level_from` int(11) default NULL,
    `level_to` int(11) default NULL,
    `show_from` datetime default NULL,
    `show_to` datetime default NULL,
    `html` text,
    `mo_html` text,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$req['pfx']}sentmail` (
    `idx` int(11) NOT NULL auto_increment,
    `template` varchar(255) default NULL,
    `to_mb` varchar(255) default NULL,
    `level_from` int(11) default NULL,
    `level_to` int(11) default NULL,
    `subject` text,
    `html` text,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$req['pfx']}sentsms` (
    `idx` int(11) NOT NULL auto_increment,
    `sendtype` varchar(255) DEFAULT NULL,
    `to_mb` varchar(255) DEFAULT NULL,
    `to_phone` text DEFAULT NULL,
    `level_from` int(11) DEFAULT NULL,
    `level_to` int(11) DEFAULT NULL,
    `subject` text DEFAULT NULL,
    `memo` text DEFAULT NULL,
    `use_resv` text DEFAULT NULL,
    `resv_date` text DEFAULT NULL,
    `resv_hour` text DEFAULT NULL,
    `resv_min` text DEFAULT NULL,
    `regdate` datetime DEFAULT NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$req['pfx']}session` (
    `idx` int(11) NOT NULL auto_increment,
    `sesskey` text NOT NULL,
    `expiry` int(11) NOT NULL,
    `value` text,
    `mb_idx` int(11) default '0',
    `ip` varchar(255) default NULL,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `{$req['pfx']}session` ADD INDEX(`mb_idx`);

CREATE TABLE IF NOT EXISTS `{$req['pfx']}config` (
    `cfg_idx` int(11) NOT NULL auto_increment,
    `cfg_type` text NOT NULL,
    `cfg_key` text NOT NULL,
    `cfg_value` text default NULL,
    `cfg_regdate` datetime default NULL,
    PRIMARY KEY  (`cfg_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$req['pfx']}config` (`cfg_type`, `cfg_key`, `cfg_value`, `cfg_regdate`) VALUES
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

CREATE TABLE IF NOT EXISTS `{$req['pfx']}sitemap` (
    `idx` int(11) NOT NULL,
    `caidx` text collate utf8_bin,
    `title` varchar(255) collate utf8_bin default NULL,
    `href` text collate utf8_bin,
    `visible` char(1) collate utf8_bin NOT NULL default 'Y',
    `children` int(11) NOT NULL default '0',
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$req['pfx']}sitemap` (`idx`, `caidx`, `title`, `href`, `visible`, `children`) VALUES
(1, '0001', 'Introduce', 'sub/view/contents', 'Y', 2),
(2, '00010001', 'zigger 소개', 'sub/view/contents', 'Y', 0),
(3, '00010002', 'Manager 소개', 'sub/view/manager', 'Y', 0),
(4, '0002', 'Community', 'sub/board/news', 'Y', 2),
(5, '00020001', 'News', 'sub/board/news', 'Y', 0),
(6, '00020002', 'Freeboard', 'sub/board/free', 'Y', 0),
(7, '0003', 'Contact', 'sub/view/contactus', 'Y', 0);

CREATE TABLE IF NOT EXISTS `{$req['pfx']}visitcount` (
    `idx` int(11) NOT NULL auto_increment,
    `mb_idx` int(11) default NULL,
    `mb_id` varchar(255) default NULL,
    `ip` varchar(255) default NULL,
    `device` varchar(255) default NULL,
    `browser` varchar(255) default NULL,
    `regdate` datetime default NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$req['pfx']}visitcount` ADD UNIQUE KEY `fkey1` (`ip`,`regdate`);
ALTER TABLE `{$req['pfx']}visitcount` ADD INDEX(`mb_idx`);

CREATE TABLE `{$req['pfx']}dataupload` (
    `idx` int(11) NOT NULL auto_increment,
    `filepath` text COLLATE utf8_bin DEFAULT NULL,
    `orgfile` text COLLATE utf8_bin DEFAULT NULL,
    `repfile` text COLLATE utf8_bin DEFAULT NULL,
    `byte` int(11) DEFAULT NULL,
    `storage` char(1) COLLATE utf8_bin NOT NULL DEFAULT 'N',
    `regdate` datetime NOT NULL,
    PRIMARY KEY  (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

";
