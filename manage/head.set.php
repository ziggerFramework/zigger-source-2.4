<?php
use Corelib\Func;
use Manage\ManageFunc;

Func::add_title('Manager');
$manage = new ManageFunc();
?>
<!DOCTYPE HTML>
<html lang="ko">
<head>
<meta NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW, NOARCHIVE" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=1400"/>
<?php if ($CONF['favicon']) { ?>
<link rel="icon" href="<?php echo $CONF['favicon']; ?>" />
<link rel="shortcut icon" href="<?php echo $CONF['favicon']; ?>" />
<?php } ?>
<link rel="stylesheet" href="<?php echo PH_DIR; ?>/layout/css/jquery.common.css<?php echo SET_CACHE_HASH; ?>" />
<link rel="stylesheet" href="<?php echo PH_DIR; ?>/manage/css/common.css<?php echo SET_CACHE_HASH; ?>" />
<link rel="stylesheet" href="<?php echo PH_DIR; ?>/manage/css/global.css<?php echo SET_CACHE_HASH; ?>" />
<link rel="stylesheet" href="<?php echo PH_PLUGIN_DIR; ?>/<?php echo PH_PLUGIN_CKEDITOR; ?>/contents_view.css<?php echo SET_CACHE_HASH; ?>" />
<script type="text/javascript">
var PH_DIR = '<?php echo PH_DIR; ?>';
var PH_DOMAIN = '<?php echo PH_DOMAIN; ?>';
var PH_PLUGIN_DIR = '<?php echo PH_PLUGIN_DIR; ?>';
var PH_MANAGE_DIR = '<?php echo PH_MANAGE_DIR; ?>';
var PH_MN_HREF = '<?php echo $REL_PATH['full_path'].'/'.$REL_PATH['page_name'].'/'.$REL_PATH['class_name'] ?>';
var PH_KPOSTCODE_API_URL = '<?php echo SET_KPOSTCODE_URL; ?>';
var PH_POST_MAX_SIZE = <?php echo $CONF['ini_post_max_size']; ?>;
</script>
<script src="<?php echo PH_DIR; ?>/layout/js/jquery.min.js<?php echo SET_CACHE_HASH; ?>"></script>
<script src="<?php echo PH_DIR; ?>/layout/js/jquery.common.js<?php echo SET_CACHE_HASH; ?>"></script>
<script src="<?php echo PH_DIR; ?>/layout/js/common.js<?php echo SET_CACHE_HASH; ?>"></script>
<script src="<?php echo PH_DIR; ?>/manage/js/global.js<?php echo SET_CACHE_HASH; ?>"></script>
<script src="<?php echo PH_PLUGIN_DIR; ?>/<?php echo PH_PLUGIN_CKEDITOR; ?>/ckeditor.js<?php echo SET_CACHE_HASH; ?>"></script>
</head>
<body>
<div id="<?php if (defined("MAINPAGE")) { echo "main"; } else { echo "sub"; } ?>">
