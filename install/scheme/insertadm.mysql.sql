<?php
$SCHEME_INSERTADM = "

insert into `".DB_PREFIX."member` (`mb_adm`, `mb_idx`, `mb_id`, `mb_email`, `mb_pwd`, `mb_name`, `mb_level`, `mb_gender`, `mb_phone`, `mb_telephone`, `mb_lately`, `mb_lately_ip`, `mb_point`, `mb_email_chk`, `mb_email_chg`, `mb_sns_ka`, `mb_sns_ka_token`, `mb_sns_nv`, `mb_sns_nv_token`, `mb_app_key`, `mb_regdate`, `mb_dregdate`, `mb_1`, `mb_2`, `mb_3`, `mb_4`, `mb_5`, `mb_6`, `mb_7`, `mb_8`, `mb_9`, `mb_10`, `mb_exp`)
values
('Y', '1', '".addslashes($req['id'])."', '', concat('*', upper(sha1(unhex(sha1('".addslashes($req['pwd'])."'))))), '".addslashes($req['name'])."', '1', 'M', NULL, NULL, NULL, NULL, '0', 'Y', '', NULL, NULL, NULL, NULL, NULL, now(), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '')

on duplicate key update
`mb_id`='".addslashes($req['id'])."',
`mb_pwd`=concat('*', upper(sha1(unhex(sha1('".addslashes($req['pwd'])."'))))),
`mb_name`='".addslashes($req['name'])."';

";
