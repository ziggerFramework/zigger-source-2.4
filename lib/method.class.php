<?php
namespace Corelib;

use Corelib\Func;

class Method {

    static function parse($var)
    {
        foreach ($var as $key => $value) {
            global $$key;
            $$key = $value;
        }
    }

    static public function request($type, $name)
    {
        $type = strtolower($type);
        $return_arr = array();

        if ($type == 'get') {

            $expl = explode(',', $name);

            if (count($expl) > 0) {
                for ($i = 0; $i < count($expl); $i++) {
                    $expl[$i] = trim($expl[$i]);
                    $return_arr[$expl[$i]] = (isset($_GET[$expl[$i]])) ? htmlspecialchars($_GET[$expl[$i]]) : null;
                }
            }
            return $return_arr;

        } else if ($type == 'post') {

            $expl = explode(',', $name);

            if (count($expl) > 0) {
                for ($i = 0; $i < count($expl); $i++) {
                    $expl[$i] = trim($expl[$i]);
                    $return_arr[$expl[$i]] = (isset($_POST[$expl[$i]])) ? $_POST[$expl[$i]] : null;
                }
            }
            return $return_arr;

        } else if ($type == 'file') {

            $expl = explode(',', $name);

            if (count($expl) > 0) {
                for ($i = 0; $i < count($expl); $i++) {
                    $expl[$i] = trim($expl[$i]);
                    $return_arr[$expl[$i]] = (isset($_FILES[$expl[$i]])) ? $_FILES[$expl[$i]] : null;
                }
            }

            return $return_arr;
        }
    }

    static function security($type)
    {
        $type = strtolower($type);

        if ($type == 'referer') {
            $referer_http_host = array();
            
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
                $referer_http_host = array_merge($referer_http_host, $hosts);

            } else if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                $referer_http_host[] = $_SERVER['HTTP_HOST'];

            } else {
                Func::core_err(ERR_MSG_1);
            }

            $match_count = 0;

            foreach ($referer_http_host as $key => $value) {
                if (isset($_SERVER['HTTP_REFERER']) && preg_match(";".trim($value).";", $_SERVER['HTTP_REFERER'])) $match_count++;
            }

            if ($match_count < 1) Func::core_err(ERR_MSG_1);

        } else if ($type == 'request_get') {
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') Func::core_err(ERR_MSG_1);
            
        } else if ($type == 'request_post') {
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') Func::core_err(ERR_MSG_1);
        }
    }
}
