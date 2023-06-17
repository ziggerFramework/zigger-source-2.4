<?php
namespace Corelib;

class Valid {

    static public $return;
    static public $msg;
    static public $location;
    static public $function;
    static public $document;
    static public $element;
    static public $input;
    static public $err_code;

    static private function trim_val($val)
    {
        return trim($val);
    }

    static public function set($arr)
    {
        foreach ($arr as $key => $value) {
            self::$$key = $value;
        }
    }

    static public function turn()
    {
        switch (self::$return) {

            case 'alert->location' :
                echo '
                    [
                        {
                            "success" : "alert->location",
                            "opt" : [
                                {
                                    "msg" : "'.self::$msg.'",
                                    "location" : "'.self::$location.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'alert->reload' :
                echo '
                    [
                        {
                            "success" : "alert->reload",
                            "opt" : [
                                {
                                    "msg" : "'.self::$msg.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'callback' :
                echo '
                    [
                        {
                            "success" : "callback",
                            "opt" : [
                                {
                                    "function" : "'.self::$function.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'callback-txt' :
                echo '
                    [
                        {
                            "success" : "callback-txt",
                            "opt" : [
                                {
                                    "msg" : "'.self::$msg.'",
                                    "element" : "'.self::$element.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'ajax-load' :
                echo '
                    [
                        {
                            "success" : "ajax-load",
                            "opt" : [
                                {
                                    "document" : "'.self::$document.'",
                                    "element" : "'.self::$element.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'ajax-validt' :
                echo '
                    [
                        {
                            "success" : "ajax-validt",
                            "opt" : [
                                {
                                    "msg" : "'.self::$msg.'"
                                }
                            ]
                        }
                    ]
                ';
                break;

            case 'none' :
                echo '
                    [
                        {
                            "success" : "none",
                            "opt" : ""
                        }
                    ]
                ';
                break;

            case 'error' :
                echo '
                    [
                        {
                            "success" : "error",
                            "opt" : [
                                {
                                    "input" : "'.self::$input.'",
                                    "err_code" : "'.self::$err_code.'",
                                    "msg" : "'.self::$msg.'"
                                }
                            ]
                        }
                    ]
                ';
                break;
        }
        exit;
    }

    // error 출력 후 스크립트 실행 멈춤
    static public function error($inp, $msg)
    {
        self::set(
            array(
                'return' => 'error',
                'input' => $inp,
                'msg' => $msg
            )
        );
        self::turn();
    }

    // 글자 수 검사
    static public function chklen($minLen, $maxLen, $val)
    {
        ob_start();
        mb_internal_encoding('UTF-8');

        return (mb_strlen($val) < $minLen || mb_strlen($val) > $maxLen) ? false : true;
    }

    // 정규식 검사
    static public function match($exp, $val)
    {
        return (preg_match($exp, $val)) ? true : false;
    }

    // 검사 수행
    static public function get($arr)
    {
        // 변수 초기화
        if (!isset($arr['input'])) $arr['input'] = '';
        if (!isset($arr['value'])) $arr['value'] = '';
        if (!isset($arr['msg'])) $arr['msg'] = '';
        if (!isset($arr['check']['null'])) $arr['check']['null'] = false;
        if (!isset($arr['check']['selected']))$arr['check']['selected'] = false;
        if (!isset($arr['check']['checked'])) $arr['check']['checked'] = false;
        if (!isset($arr['check']['chkhtml'])) $arr['check']['chkhtml'] = false;

        foreach ($arr['check'] as $key => $value) {
            switch ($key) {

                // 값이 null 인 경우 error (default : false)
                case 'null' :

                    if ($value === false && self::trim_val($arr['value']) == '') {
                        self::$err_code = (self::trim_val($arr['msg']) == '') ? 'ERR_NULL' : '';
                        self::error($arr['input'], $arr['msg']);
                    }

                    break;

                // minlen 보다 글자 수 적은 경우 error
                case 'minlen' :

                    ob_start();
                    mb_internal_encoding('UTF-8');

                    if (self::trim_val($arr['value']) != '' && mb_strlen(self::trim_val($arr['value'])) < $value) {
                        if (self::trim_val($arr['msg']) == '') $arr['msg'] = '가능한 최소 글자수는 '.$value.'자 입니다.';
                        self::error($arr['input'], $arr['msg']);
                    }

                    break;

                // mxnlen 보다 글자 수 많은 경우 error
                case 'maxlen' :

                    ob_start();
                    mb_internal_encoding('UTF-8');

                    if (mb_strlen(self::trim_val($arr['value'])) > $value) {
                        if (self::trim_val($arr['msg']) == '') $arr['msg'] = '가능한 최대 글자수는 '.$value.'자 입니다.';
                        self::error($arr['input'], $arr['msg']);
                    }

                    break;

                // minint 보다 숫자 작은 경우 error
                case 'minint' :

                    if (self::trim_val($arr['value']) != '' && (int)$arr['value'] < $value) {
                        if (self::trim_val($arr['msg']) == '') $arr['msg'] = '가능한 최소 값은 '.$value.' 입니다.';
                        self::error($arr['input'], $arr['msg']);
                    }

                    break;

                // maxint 보다 숫자 큰 경우 error
                case 'maxint' :

                    if ((int)$arr['value'] > $value) {
                        if (self::trim_val($arr['msg']) == '') $arr['msg'] = '가능한 최대 값은 '.$value.' 입니다.';
                        self::error($arr['input'], $arr['msg']);
                    }

                    break;

                // 미리 정의된 정규식에 부합하지 않은 경우 error (id, password, phone, email, nickname)
                case 'defined' :

                    $exp_arr = array(
                        'idx' => REGEXP_IDX,
                        'id' => REGEXP_ID,
                        'phone' => REGEXP_PHONE,
                        'email' => REGEXP_EMAIL,
                        'nickname' => REGEXP_NICK
                    );

                    $len_arr = array(
                        'idx' => [3, 15],
                        'id' => [5, 30],
                        'password' => [5, 50],
                        'phone' => [8, 12],
                        'nickname' => [2, 12]
                    );

                    if (self::trim_val($arr['value']) != '' && isset($exp_arr[$value])) {
                        if (!self::match($exp_arr[$value], $arr['value'])) self::error($arr['input'], $arr['msg']);
                    }
                    if (self::trim_val($arr['value']) != '' && isset($len_arr[$value])) {
                        if (!self::chklen($len_arr[$value][0], $len_arr[$value][1], $arr['value'])) self::error($arr['input'], $arr['msg']);
                    }

                    break;

                //character 유형이 일치하지 않는 경우 error (number, neganumber, korean, english)
                case 'charset' :

                    $exp_arr = array(
                        'number' => REGEXP_NUM,
                        'neganumber' => REGEXP_NEGANUM,
                        'korean' => REGEXP_KOR,
                        'english' => REGEXP_ENG
                    );

                    if (isset($exp_arr[$value]) && !self::match($exp_arr[$value], $arr['value'])) self::error($arr['input'], $arr['msg']);

                    break;

                // 사용 금지 HTML TAG 포함된 경우 error (default : false)
                case 'chkhtml' :

                    if ($value === true) {
                        $not_tags = SET_INTDICT_TAGS;
                        $not_tags_ex = explode(',', $not_tags);

                        for ($i = 0; $i < count($not_tags_ex); $i++) {
                            if (stristr($arr['value'], '<'.$not_tags_ex[$i]) || stristr($arr['value'], '</'.$not_tags_ex[$i])) {
                                if (self::trim_val($arr['msg']) == '') $arr['msg'] = ERR_MSG_2;
                                self::error($arr['input'], $arr['msg']);

                                return;
                            }
                        }
                    }

                    break;

                // selected 선택 안된 경우 error (default : false)
                case 'selected' :

                    if ($value === true && ($arr['value'] == 'none' || $arr['value'] == '')) self::error($arr['input'], $arr['msg']);

                    break;

                // checked 선택 안된 경우 error
                case 'checked' :

                    if ($value === true && $arr['value'] != 'checked') self::error($arr['input'], $arr['msg']);

                    break;

                // 사용자 정의 정규식에 부합하지 않는 경우 error
                case 'regexp' :

                    $bool = true;

                    if ($value[0] === true && !self::match($value[1], $arr['value'])) {
                        $bool = false;
                    }
                    else if ($value[0] === false && self::match($value[1], $arr['value'])) {
                        $bool = false;
                    }

                    if ($bool === false) self::error($arr['input'], $arr['msg']);

                    break;
            }
        }
    }
}
