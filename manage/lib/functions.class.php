<?php
namespace Manage;

use Corelib\Valid;
use Corelib\Func;
use Corelib\Method;

class ManageFunc{

    public function __construct()
    {
        global $REQUEST, $PARAM, $MB, $keyword, $searchby;

        $PARAM = Method::request('get', 'mod, href, p, sort, ordtg, ordsc, where, keyword, page');

        foreach ($PARAM as $key => $value) {
            if (!empty($value)) {
                if (in_array($key, array('sort', 'ordtg', 'where', 'ordsc')) && !preg_match('/^[a-zA-Z0-9._-]+$/', $value)) {
                    $PARAM[$key] = null;
                    if ($key == 'where') $PARAM['keyword'] = null;
                    if ($key == 'ordtg') $PARAM['ordsc'] = null;
                }
                if (in_array($key, array('ordsc')) && !in_array(strtolower($value), array('asc', 'desc'))) {
                    $PARAM[$key] = 'desc';
                }
            } 
        }

        if ($REQUEST['rewritetype'] == 'submit') {
            if ($MB['level'] > 1) Valid::error('', '관리자만 접근 가능합니다.');

        } else {
            Func::getlogin('관리자만 접근 가능합니다.', PH_MANAGE_DIR);
            Func::chklevel(1);
        }

        $keyword = (!empty($PARAM['keyword'])) ? urldecode(addslashes($PARAM['keyword'])) : '';
        $searchby = '';
        if (!empty($PARAM['keyword']) && trim(addslashes($PARAM['keyword'])) != '') $searchby = 'AND '.addslashes($PARAM['where']).' like \'%'.$keyword.'%\'';

    }

    public function gosite()
    {
        return PH_DOMAIN;
    }

    public function signout_link()
    {
        return PH_DIR.'/sign/signout';
    }

    public function adminfo_link()
    {
        return PH_MANAGE_DIR.'/adm/info';
    }

    public function module_total()
    {
        global $MODULE;

        return count($MODULE);
    }

    public function req_hidden_inp($type)
    {
        global $PARAM;

        $PARAM = Method::request($type, 'page, mode, sort, ordtg, ordsc, where, keyword');
    }

    public function retlink($param)
    {
        global $PARAM;

        return '?page='.$PARAM['page'].'&sort='.$PARAM['sort'].'&ordtg='.$PARAM['ordtg'].'&ordsc='.$PARAM['ordsc'].'&where='.$PARAM['where'].'&keyword='.$PARAM['keyword'].$param;
    }

    public function href_type()
    {
        global $PARAM;

        if (isset($PARAM['mod']) && $PARAM['mod'] != '') {
            return 'mod';

        } else if (isset($PARAM['href']) && $PARAM['href'] != '') {
            return 'def';
        }
    }

    public function sch_where($wh)
    {
        global $PARAM;

        if ($PARAM['where'] == $wh) return 'selected';
    }

    public function sortlink($param)
    {
        return $param;
    }

    public function orderlink($tg)
    {
        global $PARAM;

        $sc = ($PARAM['ordtg'] == $tg && $PARAM['ordsc'] == 'asc') ? 'desc' : 'asc';
        $sch = ($PARAM['keyword']) ? '&where='.$PARAM['where'].'&keyword='.urlencode($PARAM['keyword']) : '';

        $etc_var = '';

        if (isset($PARAM[0])) {
            foreach ($PARAM[0] as $key => $value) {
                $etc_var .= '&'.$key.'='.$value;
            }
        }

        return $this->sortlink('?sort='.$PARAM['sort'].'&ordtg='.$tg.'&ordsc='.$sc.$sch.$etc_var);
    }

    public function pag_def_param()
    {
        global $PARAM;

        return '&sort='.$PARAM['sort'].'&ordtg='.$PARAM['ordtg'].'&ordsc='.$PARAM['ordsc'].'&where='.$PARAM['where'].'&keyword='.urlencode(!empty($PARAM['keyword']) ? $PARAM['keyword'] : '');
    }

    public function lnk_def_param($params = '')
    {
        global $PARAM;

        return '?page='.$PARAM['page'].'&sort='.$PARAM['sort'].'&ordtg='.$PARAM['ordtg'].'&ordsc='.$PARAM['ordsc'].'&where='.$PARAM['where'].'&keyword='.urlencode(!empty($PARAM['keyword']) ? $PARAM['keyword'] : '').$params;
    }

    public function print_hidden_inp()
    {
        global $PARAM;

        echo '
            <input type="hidden" name="page" value="'.$PARAM['page'].'" />
            <input type="hidden" name="sort" value="'.$PARAM['sort'].'" />
            <input type="hidden" name="ordtg" value="'.$PARAM['ordtg'].'" />
            <input type="hidden" name="ordsc" value="'.$PARAM['ordsc'].'" />
        ';
    }

    public function make_target($tab)
    {
        global $target_tabs;
        $target_tabs = explode('|', $tab);
    }

    public function print_target()
    {
        global $target_tabs;

        $tab_arr = array();

        for ($i = 0; $i < count($target_tabs); $i++) {
            $html = '<ul id="target'.$i.'" class="tab1">';

            for ($j = 0; $j < count($target_tabs); $j++) {
                $chked = '';
                if ($j == $i) $chked = 'on';
                $html .= '<li class="'.$chked.'"><a href="#target'.$j.'">'.$target_tabs[$j].'</a></li>';
            }
            $html .= '</ul>';
            $tab_arr[$i] = $html;
        }

        return $tab_arr;
    }

}
