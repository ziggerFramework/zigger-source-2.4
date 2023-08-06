<?php
namespace Controller;

use Corelib\Func;
use Make\View\Layout;
use Make\Database\Pdosql;

class Make_Controller {

    static public $ob_head_html;
    static public $ob_body_html;
    static public $ob_foot_html;
    static private $ob_src_css;
    static private $ob_src_js;
    static private $ob_title;
    static private $ob_ogtitle;
    static private $ob_define_js;
    static private $ob_body_class;
    static private $org_head_html;
    static private $setparm = array();
    static private $layout_type;
    private $layout;

    static public function set_ob()
    {
        global $ob_src_css, $ob_src_js, $ob_title, $ob_ogtitle, $ob_define_js, $ob_body_class;

        self::$ob_src_css = $ob_src_css;
        self::$ob_src_js = $ob_src_js;
        self::$ob_title = $ob_title;
        self::$ob_ogtitle = $ob_ogtitle;
        self::$ob_define_js = $ob_define_js;
        self::$ob_body_class = $ob_body_class;
    }

    public function set($name, $parm)
    {
        self::$setparm[$name] = $parm;
    }

    public function layout()
    {
        self::set_ob();
        self::$layout_type = 'layout';
        $this->layout = new Layout();

        return $this;
    }

    public function category_key($key)
    {
        global $NAVIGATOR;

        $sql = new Pdosql();

        $sql->query(
            "
            select *
            from {$sql->table("sitemap")}
            where idx=:col1
            ",
            array(
                $key
            )
        );
        $caidx = $sql->fetch('caidx');
        $caidx_len = strlen($caidx);
        $cat_dep = $caidx_len / 4;
        $catinfo = array();

        for ($i = 1; $i <= $cat_dep; $i++) {
            $sql->query(
                "
                select *
                from {$sql->table("sitemap")}
                where caidx=:col1
                ",
                array(
                    substr($caidx, 0, 4 * $i)
                )
            );
            $catinfo[$i - 1] = $sql->fetchs();
        }
        $NAVIGATOR = $catinfo;

        Func::define_javascript('PH_CATEGORY_KEY', $key);
    }

    public function common()
    {
        self::set_ob();
        self::$layout_type = 'common';
        $this->layout = new Layout();

        return $this;
    }

    public function view($viewer = '', $makedo = true, $makename = 'make')
    {
        global $CONF, $MB, $MODULE, $THEME, $NAVIGATOR;

        if ($makedo === true) $this->{$makename}();
        if ($viewer != '') $this->get_viewer($viewer);
    }

    public function head($type = '')
    {
        global $CONF, $MB, $MODULE, $THEME, $NAVIGATOR;

        ob_start();

        if (self::$layout_type == 'layout') require_once PH_PATH.'/layout/head.php';
        if (self::$layout_type == 'common') require_once PH_PATH.'/layout/head.set.php';
        self::$ob_head_html = ob_get_contents();

        ob_end_clean();
        ob_start();
    }

    public function mng_head()
    {
        global $CONF, $MB, $MODULE, $THEME, $PARAM, $REL_PATH;

        ob_start();

        if (self::$layout_type == 'layout') require_once PH_MANAGE_PATH.'/head.php';
        if (self::$layout_type == 'common') require_once PH_MANAGE_PATH.'/head.set.php';
        self::$ob_head_html = ob_get_contents();

        ob_end_clean();
        ob_start();
    }

    public function foot($type = '')
    {
        global $CONF, $MB, $MODULE, $THEME, $CATEGORY;

        self::$ob_body_html = ob_get_contents();

        ob_end_clean();
        ob_start();

        if (self::$layout_type == 'layout') require_once PH_PATH.'/layout/foot.php';
        if (self::$layout_type == 'common') require_once PH_PATH.'/layout/foot.set.php';
        self::$ob_foot_html = ob_get_contents();

        ob_end_clean();

        echo self::print_headsrc();
        echo self::$ob_body_html;
        echo self::$ob_foot_html;
    }

    public function mng_foot()
    {
        global $CONF, $MB, $MODULE, $THEME, $PARAM;

        self::$ob_body_html = ob_get_contents();

        ob_end_clean();
        ob_start();

        if (self::$layout_type == 'layout') require_once PH_MANAGE_PATH.'/foot.php';
        if (self::$layout_type == 'common') require_once PH_MANAGE_PATH.'/foot.set.php';
        self::$ob_foot_html = ob_get_contents();

        ob_end_clean();

        echo self::print_headsrc();
        echo self::$ob_body_html;
        echo self::$ob_foot_html;
    }

    static public function print_headsrc()
    {
        global $CONF;

        $html = self::$ob_head_html;

        // <body>에 class 추가
        $html = (self::$ob_body_class) ? preg_replace("/<body>/", "<body class=\"".self::$ob_body_class."\">", $html) : $html;

        // 마지막 <link> 와 <script> 사이에 stylesheet 추가
        $html = preg_replace("#(<script type=\"text/javascript\">[^<]*var PH_DIR[^>]+)#", self::$ob_src_css."$1", $html);

        // 마지막 <script> 와 </head> 사이에 javascript 추가
        $html = preg_replace("#(</head>[^<]*<body)#", self::$ob_src_js."$1", $html);

        // 마지막 'var PH_DOMAIN' 와 </script> 사이에 javascript 전역변수 추가
        $html = preg_replace("#(var PH_DIR[^>]+;[^<]*var PH_DOMAIN[^>]+;)#", "$1".self::$ob_define_js, $html);

        // <head> 바로 아래에 title 추가
        if (self::$ob_title != '') {
            $html = preg_replace("#(<head>[^<]*)#", "$1".self::$ob_title, $html);

        } else {
            $html = preg_replace("#(<head>[^<]*)#", "$1<title>{$CONF['title']}</title>".PHP_EOL, $html);
        }

        // <meta property="og:description"> 바로 위에 og:title 추가
        if (self::$ob_ogtitle != '') {
            $html = preg_replace("#(<meta property=\"og:description\"*)#", self::$ob_ogtitle."$1", $html);

        } else {
            $html = preg_replace("#(<meta property=\"og:description\"*)#", "<meta property=\"og:title\" content=\"{$CONF['og_title']}\" />".PHP_EOL."$1", $html);
        }

        return $html;
    }

    public function get_viewer($tpl)
    {
        global $CONF, $MB, $MODULE, $THEME, $NAVIGATOR;

        extract(self::$setparm);

        if ($tpl != '') {
            if (!file_exists($tpl)) Func::core_err('View 파일이 존재하지 않습니다. ('.$tpl.')');
            
            // module view가 요청된 경우 <body>에 class 추가
            if (preg_match('/\/theme\/'.$CONF['theme'].'\/mod-.+?\//', $tpl, $matches)) {
                $mod_name = $matches[0];
                $mod_name = str_replace(array('/theme/'.$CONF['theme'], '/'), array('', ''), $mod_name);
                if ($mod_name) Func::add_body_class($mod_name);
            }

            // module manage view가 요청된 경우 manager <body>에 class 추가
            if (preg_match('/\/mod\/[\w-]+\/manage\.set\/html\//', $tpl, $matches)) {
                $mod_name = $matches[0];
                $mod_name = str_replace(array('/mod/', '/manage.set/html/'), array('', ''), $mod_name);
                if ($mod_name) Func::add_body_class('mod-'.$mod_name.'-manage');
            }
            
            require $tpl;
        }
    }

}

class Make_Module_Controller {

    public $set = array();

    function __construct()
    {
        spl_autoload_register('mod_autoloader');
    }

    public function set($key, $val)
    {
        $this->set[$key] = $val;
    }

    public function configure()
    {
        global $MOD_CONF;

        $MOD_CONF = $this->set;
    }

}

class Make_View_Form {

    public $set = array();

    public function set($key, $val)
    {
        $this->set[$key] = $val;
    }

    public function run()
    {
        global $REL_PATH;

        $form_id = $REL_PATH['class_name'].'Form';

        if (!empty($this->set['id'])) {
            $form_id = $this->set['id'];

        } else if (!isset($this->set['type'])) {
            $this->set['type'] = 'html';

        }

        if (!isset($this->set['action'])) $this->set['action'] = '';
        if (!isset($this->set['method'])) $this->set['method'] = 'post';
        if (!isset($this->set['target'])) $this->set['target'] = '';

        $form_attr = array(
            'static' => 'action="'.$this->set['action'].'" method="'.$this->set['method'].'"',
            'html' => 'ajax-action="'.$this->set['action'].'?rewritetype=submit" ajax-type="'.$this->set['type'].'"',
            'multipart' => 'enctype="multipart/form-data" ajax-action="'.$this->set['action'].'?rewritetype=submit" ajax-type="'.$this->set['type'].'"'
        );

        echo 'name="'.$form_id.'" id="'.$form_id.'" '.$form_attr[$this->set['type']];
    }

}

class Make_View_Fetch {

    public $set = array();

    public function set($key, $val)
    {
        $this->set[$key] = $val;
    }

    public function configure()
    {
        global $FETCH_CONF;

        $FETCH_CONF = $this->set;
    }

    public function run()
    {
        require_once $this->set['doc'];

        if (!empty($this->set['className'])) {
            $className = $this->set['className'];

        } else {
            $basename = basename($this->set['doc']);
            $className = substr($basename, 0, strrpos($basename, '.'));
        }

        $this->configure();

        $className = str_replace(
            array('-', '.'),
            array('_', '_'),
            $className
        );

        $$className = new $className();
        $$className->init();
    }
}
