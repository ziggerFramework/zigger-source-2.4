<?php
namespace Corelib;

use Make\Database\Pdosql;
use Make\Library\Sms;

class Func {

    static public function chk_update_config_field($fields)
    {
        global $CONF;

        foreach ($fields as $key => $value) {

            $field_value = '';
            $field_key = $value;

            if (strstr($value, ':')) {
                $val_exp = explode(':', $value);
                $field_value = $val_exp[1];
                $field_key = $val_exp[0];
            }

            if (!isset($CONF[$field_key])) {
                $sql = new Pdosql();
                $sql->query(
                    "
                    insert into
                    {$sql->table("config")}
                    (cfg_type, cfg_key, cfg_value, cfg_regdate)
                    values
                    ('engine', :col1, :col2, now())
                    ", array(
                        $field_key, $field_value
                    )
                );
            }

        }

        return true;
    }

    static public function add_stylesheet($file)
    {
        global $ob_src_css;

        if (strstr((!empty($ob_src_css)) ? $ob_src_css : '', $file)) return false;
        $ob_src_css .= '<link rel="stylesheet" href="'.$file.'"/>'.PHP_EOL;

        return true;
    }

    static public function add_javascript($file)
    {
        global $ob_src_js;

        if (strstr((!empty($ob_src_js)) ? $ob_src_js : '', $file)) return false;
        $ob_src_js .= '<script src="'.$file.'"></script>'.PHP_EOL;
        
        return true;
    }

    static public function define_javascript($name, $val)
    {
        global $ob_define_js;

        $ob_define_js .= PHP_EOL.'var '.$name.' = "'.$val.'";';

        return true;
    }

    static public function print_javascript($source)
    {
        global $ob_src_js;

        $ob_src_js .= '<script type="text/javascript">'.PHP_EOL;
        $ob_src_js .= $source.PHP_EOL;
        $ob_src_js .= '</script>'.PHP_EOL;

        return true;
    }

    static public function add_title($title)
    {
        global $CONF, $ob_title, $ob_ogtitle;

        $ob_title = '<title>'.$CONF['title'].' - '.$title.'</title>'.PHP_EOL;
        $ob_ogtitle = '<meta property="og:title" content="'.$CONF['og_title'].' - '.$title.'" />'.PHP_EOL;

        return true;
    }

    static public function add_body_class($class)
    {
        global $ob_body_class;
        
        if (!$ob_body_class && $class) $ob_body_class = $class;
        if ($ob_body_class && !preg_match("/.*(?:^| )".$class."(?:$| ).*/", $ob_body_class)) $ob_body_class .= ' '.$class;

        return true;
    }

    // page key 셋팅
    static public function set_category_key($key)
    {
        define('SET_CATEGORY_KEY', $key);

        return true;
    }

    // Date Format (날짜만)
    static public function date($str)
    {
        return ($str != '') ? date(SET_DATE,strtotime($str)) : '';
    }

    // Date Format (날짜와 시간)
    static public function datetime($str)
    {
        return ($str != '') ? date(SET_DATETIME,strtotime($str)) : '';
    }

    // Number로 치환
    static public function number($str)
    {
        return number_format((int)$str);
    }

    // 파일 사이즈 단위 계산
    static public function getbyte($size, $byte, $comma = true)
    {
        $byte = strtolower($byte);

        $divisors = array('k' => 1024, 'm' => 1024 * 1024, 'g' => 1024 * 1024 * 1024);
        $divisor = isset($divisors[$byte]) ? $divisors[$byte] : 1;
        $size = (int)$size / $divisor;
        $size = ($comma === true) ? number_format($size, 1) : $size;

        return $size;
    }

    // file_get_contents 대체 함수 (curl)
    static public function url_get_contents($url, $post = false, $headers = null, $bodys = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, SET_CURLOPT_CONNECTTIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, SET_CURLOPT_SSL_VERIFYPEER);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, SET_CURLOPT_RETURNTRANSFER);
        curl_setopt($ch, CURLOPT_POST, ($post === true) ? true : false);
        if (isset($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (isset($bodys)) curl_setopt($ch, CURLOPT_POSTFIELDS, $bodys);
        $output = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $return_arr = array(
            'status_code' => $status_code,
            'data' => json_decode($output, true)
        );

        return $return_arr;
    }

    // 로그인이 되어있지 않다면 로그인 화면으로 이동
    static public function getlogin($msg, $url = null)
    {
        if (IS_MEMBER) return;

        if ($msg) self::alert($msg);
        $url = (!empty($url)) ? $url : $_SERVER['REQUEST_URI'];
        self::location_parent(PH_DOMAIN.'/sign/signin?redirect='.urlencode($url));

        return false;
    }

    // 회원 level 체크
    static public function chklevel($level)
    {
        global $MB;

        if ($MB['level'] > $level) self::err_back(ERR_MSG_10);

        return false;
    }

    // device 체크
    static public function chkdevice()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;

        $mobile = explode(',', SET_MOBILE_DEVICE);
        $chk_count = 0;

        for ($i = 0; $i < count($mobile); $i++) {
            if (preg_match("/$mobile[$i]/", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $chk_count++;
                break;
            }
        }

        return ($chk_count > 0) ? 'mobile' : 'pc';
    }

    // 문자열 유효성 검사
    static public function chkintd($type, $val, $intd)
    {
        $intd = explode(',', $intd);
        $chk = true;

        for ($i = 0; $i <= sizeof($intd) - 1; $i++) {
            if (strpos($val, trim($intd[$i])) !== false) $chk = false;
        }
        if ($type == 'notmatch') {
            return $chk !== false;
            
        } else if ($type == 'match') {
            return $chk === false;
        }
    }

    // 문자열 자르기
    static public function strcut($str, $start, $end)
    {
        $cutstr = mb_substr($str, $start, $end, 'UTF-8');

        return (strlen($cutstr) < strlen($str)) ? $cutstr.'···' : $cutstr;
    }

    // 회원 포인트 적립 or 차감 처리
    static public function set_mbpoint($arr)
    {
        if (!$arr['mb_idx'] || $arr['mb_idx'] < 1 || $arr['point'] < 1) return;

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("member")}
            where mb_idx=:col1
            ",
            array(
                $arr['mb_idx']
            )
        );
        $mb_point = $sql->fetch('mb_point');

        if ($arr['mode'] == 'in') {
            $set_point = (int)$mb_point + (int)$arr['point'];
            $sql->query(
                "
                insert into {$sql->table("mbpoint")}
                (mb_idx,p_in, memo, regdate)
                values
                (:col1, :col2, :col3, now())
                ",
                array(
                    $arr['mb_idx'],
                    $arr['point'],
                    $arr['msg']
                )
            );
        }

        if ($arr['mode'] == 'out') {

            if ($mb_point < $arr['point']) {
                $set_point = 0;
                $arr['msg'] .= ' (차감할 포인트 부족으로 0 처리)';

            } else {
                $set_point = $mb_point - $arr['point'];
            }

            $sql->query(
                "
                insert into {$sql->table("mbpoint")}
                (mb_idx, p_out, memo, regdate)
                values
                (:col1, :col2, :col3, now())
                ",
                array(
                    $arr['mb_idx'],
                    $arr['point'],
                    $arr['msg']
                )
            );
        }

        $sql->query(
            "
            update {$sql->table("member")}
            set mb_point=:col1
            where mb_idx=:col2
            ",
            array(
                $set_point,
                $arr['mb_idx']
            )
        );

        return true;
        
    }

    // 관리자 최근 피드에 등록
    static public function add_mng_feed($arr)
    {
        global $CONF;

        $sql = new Pdosql();

        $sql->query(
            "
            insert into {$sql->table("mng_feeds")}
            (msg_from, memo, href, regdate)
            values
            (:col1, :col2, :col3, now())
            ",
            array(
                $arr['from'],
                $arr['msg'],
                $arr['link']
            )
        );

        if ($CONF['use_feedsms'] == 'Y') {
            $sms = new Sms();

            $sms_toadm = array();

            if (strstr($CONF['sms_toadm'], ',')) {
                $exp = explode(',', $CONF['sms_toadm']);
                foreach ($exp as $number) {
                    $sms_toadm[] = trim($number);
                }
            } else {
                $sms_toadm[] = trim($CONF['sms_toadm']);
            }

            $sms->set(
                array(
                    'memo' => '[zigger] '.strip_tags($arr['msg']),
                    'to' => $sms_toadm
                )
            );
            $sms->send();
        }

        return true;

    }

    // parameter 조합
    static public function get_param_combine($param, $chain = '')
    {
        $paramArr = array();

        if (preg_match('/^[?&]/', $param)) {
            $param = substr($param, 1);
        }
        
        $paramArr = array_filter(explode('&', $param), function ($list) {
            if ($list == '') return false;
            list($key, $value) = explode('=', $list, 2);
            return !empty($value) ? $key . '=' . $value : false;
        });

        return (count($paramArr) > 0) ? $chain.implode('&', $paramArr) : '';
    }

    // captcha 출력 및 검증
    static public function get_captcha($id = 'captcha', $type = 1)
    {
        global $CONF, $PLUGIN_CAPTCHA_CONF;

        $id = empty($id) ? 'captcha' : $id;

        $PLUGIN_CAPTCHA_CONF['id'] = $id;

        // google recaptcha 사용
        if ($CONF['use_recaptcha'] == 'Y') {
            self::add_javascript(SET_GRECAPTCHA_URL[0]);
            self::print_javascript('var g_recaptcha_correctCaptcha=function(){$("#g-recaptcha").next("textarea").val($("#g-recaptcha-response").val())}');
            $html = '<div class="g-recaptcha" id="g-recaptcha" data-sitekey="'.$CONF['recaptcha_key1'].'" data-callback="g_recaptcha_correctCaptcha"></div>';
            $html .= '<textarea name="'.$id.'" id="'.$id.'" style="display: none;"></textarea>';

        }

        // 기본 captcha 플러그인 사용
        else {
            require_once PH_PLUGIN_PATH.'/'.PH_PLUGIN_CAPTCHA.'/securimage.php';
            $opt = array(
                'input_name' => $id,
                'disable_flash_fallback' => true
            );

            $html = '<div id="zigger-captcha">';
            $html .= \Securimage::getCaptchaHtml($opt);
            $html .= '<input type="text" name="'.$id.'" id="'.$id.'" class="inp" value="" />';
            $html .= '</div>';
        }
        return $html;
    }

    static public function chk_captcha($val)
    {
        global $CONF;

        // google recaptcha 검증
        if ($CONF['use_recaptcha'] == 'Y') {
            $url = SET_GRECAPTCHA_URL[1].$CONF['recaptcha_key2'].'&response='.$val.'&remoteip='.$_SERVER['REMOTE_ADDR'];
            $req = self::url_get_contents($url);

            return ($req['data']['success']) ? true : false;
        }

        // 기본 captcha 플러그인 검증
        else {
            require_once PH_PLUGIN_PATH.'/'.PH_PLUGIN_CAPTCHA.'/securimage.php';
            $securimage = new \Securimage();

            return ($securimage->check($val) === true) ? true : false;
        }
    }

    // 파일 확장자 추출
    static public function get_filetype($file)
    {
        $fn = explode('.', $file);
        $fn = array_pop($fn);

        return strtolower($fn);
    }

    // 파일 upload data 정보
    static public function get_fileinfo($file, $detail = true)
    {
        global $CONF;

        $return = '';

        // detail mode
        if ($detail === true) {

            $sql = new Pdosql();
            $sql->query(
                "
                select *
                from {$sql->table("dataupload")}
                where repfile=:col1
                ", array(
                    $file
                )
            );
            $arr = $sql->fetchs();

            if ($sql->getcount() < 1) return false;

            $orglink = PH_DATA_DIR.$arr['filepath'].'/'.$arr['orgfile'];
            $replink = PH_DATA_DIR.$arr['filepath'].'/'.$arr['repfile'];

            if ($arr['storage'] == 'Y') {
                $orglink = $CONF['s3_key1'].'/'.$CONF['s3_key2'].$arr['filepath'].'/'.$arr['orgfile'];
                $replink = $CONF['s3_key1'].'/'.$CONF['s3_key2'].$arr['filepath'].'/'.$arr['repfile'];
            }

            $data = array(
                'filepath' => $arr['filepath'],
                'orgfile' => $arr['orgfile'],
                'repfile' => $arr['repfile'],
                'replink' => $replink,
                'storage' => $arr['storage'],
                'byte' => $arr['byte'],
                'regdate' => $arr['regdate']
            );

            $return = $data;

        }

        // simple mode
        else {

            $storage = 'N';

            $fileType = Func::get_filetype($file);

            if (substr(str_replace('.'.$fileType, '', $file), -1, 1) == 'Y') $storage = 'Y';

            $data = array(
                'storage' => $storage,
                'repfile' => $file
            );

            $return = $data;

        }

        return $return;
    }

    // php_ini의 post_max_size 값 반환 (M 단위 출력)
    static public function ini_post_max_size()
    {
        $max_size = @ini_get('post_max_size');

        if (!$max_size) return 0;

        $unit = strtoupper(substr($max_size, -1));
        $value = substr($max_size, 0, -1);

        switch ($unit) {
            case 'G':
                $value *= 1024;
                break;
            case 'K':
                $value /= 1024;
                break;
        }

        return $value;
    }

    // 현재 PHP 파일명 반환
    static public function thispage()
    {
        return basename($_SERVER['PHP_SELF']);
    }

    // 현재 PHP 경로(Directory) 반환
    static public function thisdir()
    {
        return str_replace('/'.basename(self::thisuri()), '', self::thisuri());
    }

    // 현재 URI 반환
    static public function thisuri($fancyQry = '')
    {
        if (!strstr($_SERVER['QUERY_STRING'], 'rewritepage=')) return '/';

        $uri = $_SERVER['REQUEST_URI'];
        $qry = $_SERVER['QUERY_STRING'];

        // rewriterule 로 url이 변조되어 실제 브라우저에서 노출되는 query_string과 다른 경우를 위한 처리
        if ($fancyQry != '') $qry = str_replace($fancyQry, '', $qry);
        $qry = substr($qry, strpos($_SERVER['QUERY_STRING'], '&') + 1);
        $uri = str_replace('?'.$qry, '', $uri);

        // uri 끝에 숫자만 존재한다면 path 에서 제외 (get parameter로 간주)
        $uri_exp = explode('/', $uri);

        if (preg_match("/^[0-9]+$/", $uri_exp[count($uri_exp) - 1])) {
            unset($uri_exp[count($uri_exp) - 1]);
            $uri = implode('/', $uri_exp);
        }

        return $uri;
    }

    // 현재 URI 반환 (쿼리 포함)
    static public function thisuriqry()
    {
        $uri = $_SERVER['REQUEST_URI'];
        return $uri;
    }

    // 현재 Controller명 반환
    static public function thisctrlr()
    {
        global $REL_PATH;
        return $REL_PATH['page_name'];
    }

    // 현재 Class명 반환
    static public function thisclass()
    {
        global $REL_PATH;
        return $REL_PATH['class_name'];
    }

    // htmlspecialchars_decode 리턴 함수 (mysql에서 Array된 변수값은 htmlspecialchars 기본 적용)
    static public function htmldecode($val)
    {
        return self::deHtmlspecialchars($val);
    }

    // deHtmlspecialchars 함수
    static public function deHtmlspecialchars($val)
    {
        return htmlspecialchars_decode($val);
    }

    // br2nl 함수
    static public function br2nl($val)
    {
        return preg_replace("/\<br(\s*)?\/?\>/i", '\n', $val);
    }

    // 중복되지 않는 pk 문자열 생성 함수
    static function make_random_char($length = 30)
    {
        $length = ($length < 30) ? 30 : $length;
        $length = $length - 19;

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $max)];
        }

        $microtime = str_replace(array(' ', '.'), array('', ''), microtime());
        
        return str_shuffle($randomString.$microtime);
    }

    // error : core error
    static public function core_err($msg, $exit = true)
    {
        global $REQUEST;

        if (isset($REQUEST['rewritetype']) && $REQUEST['rewritetype'] == 'submit') {
            echo $msg;

        } else {
            echo '<div style="border-left: 4px solid #b82e24;background: #e54d42;padding: 3px 15px;margin:15px;">';
            echo '<p style="display: block;font-size: 13px;line-height:18px;color: #fff;letter-spacing: -1px;">Core error : '.$msg.'</p>';
            echo '</div>';
        }

        if ($exit === true) exit;
    }

    // error : 오류메시지 화면에 출력
    static public function err_print($msg)
    {
        echo $msg;
        exit;
    }

    // error : alert만 띄움
    static public function err($msg)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert(\''.$msg.'\');</script>';
        exit;
    }

    // error : alert 띄운 뒤 뒤로 이동
    static public function err_back($msg)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert(\''.$msg.'\');history.back();</script>';
        exit;
    }

    // error : alert 띄운 뒤 설정한 페이지로 이동
    static public function err_location($msg,$url)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert(\''.$msg.'\');location.href=\''.$url.'\';</script>';
        exit;
    }

    // error : alert 띄운 뒤 윈도우 창 닫음
    static public function err_close($msg)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert(\''.$msg.'\');self.close();</script>';
        exit;
    }

    // exit 없는 alert 띄움
    static public function alert($msg)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert(\''.$msg.'\');</script>';
    }

    // 페이지 이동
    static public function location($url)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">location.href=\''.$url.'\';</script>';
        exit;
    }

    // 페이지 이동(_parent)
    static public function location_parent($url)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">parent.location.href=\''.$url.'\';</script>';
        exit;
    }

}
