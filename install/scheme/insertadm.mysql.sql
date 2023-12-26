<?php
$SCHEME_INSERTADM = "

INSERT INTO `".DB_PREFIX."member` (`mb_adm`, `mb_idx`, `mb_id`, `mb_email`, `mb_pwd`, `mb_name`, `mb_level`, `mb_gender`, `mb_phone`, `mb_telephone`, `mb_lately`, `mb_lately_ip`, `mb_point`, `mb_email_chk`, `mb_email_chg`, `mb_sns_ka`, `mb_sns_ka_token`, `mb_sns_nv`, `mb_sns_nv_token`, `mb_app_key`, `mb_regdate`, `mb_dregdate`, `mb_1`, `mb_2`, `mb_3`, `mb_4`, `mb_5`, `mb_6`, `mb_7`, `mb_8`, `mb_9`, `mb_10`, `mb_exp`)
VALUES
('Y', '1', '{$req['id']}', '', password('{$req['pwd']}'), '{$req['name']}', '1', 'M', NULL, NULL, NULL, NULL, '0', 'Y', '', NULL, NULL, NULL, NULL, NULL, now(), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '')

ON DUPLICATE KEY UPDATE
`mb_id`='{$req['id']}',
`mb_pwd`=CONCAT('*', UPPER(SHA1(UNHEX(SHA1('{$req['pwd']}'))))),
`mb_name`='{$req['name']}';

";
