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
                    $return_arr[$expl[$i]] = (isset($_GET[$expl[$i]])) ? $_GET[$expl[$i]] : null;
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
            if (!isset($_SERVER['HTTP_REFERER']) || !preg_match(";{$_SERVER['HTTP_HOST']};", $_SERVER['HTTP_REFERER'])) Func::core_err(ERR_MSG_1);

        } elseif ($type == 'request_get') {
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') Func::core_err(ERR_MSG_1);
            
        } elseif ($type == 'request_post') {
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') Func::core_err(ERR_MSG_1);
        }
    }
}
