<?php
namespace Corelib;

class Session {

    static function set_sess($name, $val)
    {
        if ($name == 'MB_IDX') SessionHandler::$dbinfo['mb_idx'] = $val;
        $_SESSION[$name] = $val;
    }

    static function empty_sess($name)
    {
        global $_SESSION;

        if ($name == 'MB_IDX') SessionHandler::$dbinfo['mb_idx'] = 0;
        unset($_SESSION[$name]);
    }

    static function drop_sess()
    {
        session_destroy();
    }

    static function sess($name)
    {
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    static function is_sess($name)
    {
        return (isset($_SESSION[$name])) ? true : false;
    }

}

class SessionHandler extends \Make\Database\Pdosql {

    private $value;
    private $sess_life = SET_SESS_LIFE;
    private $expiry;
    static public $dbinfo = array();

    public function open()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($key)
    {
        $this->query(
            "
            select *
            from {$this->table("session")}
            where sesskey=:col1 and expiry>
            ".time(),
            array(
                $key
            )
        );
        $this->specialchars = 0;
        $this->nl2br = 0;

        if ($this->getcount() > 0) {
            return $this->fetch('value');

        } else {
            $this->expiry = time() + $this->sess_life;
            $this->query(
                "
                insert into {$this->table("session")}
                (sesskey, expiry, value, mb_idx, ip, regdate)
                VALUES
                (:col1, :col2, 0, 0, :col3, now())
                ",
                array(
                    $key,
                    $this->expiry,
                    $_SERVER['REMOTE_ADDR']
                )
            );

            return $this->fetch('value');
        }
        return true;
    }

    public function write($key, $val)
    {
        $this->value = $val;
        $this->expiry = time() + $this->sess_life;

        if (isset(self::$dbinfo['mb_idx'])) {
            $this->query(
                "
                update {$this->table("session")}
                set expiry=:col1, value=:col2, regdate=now(), mb_idx=:col3
                where sesskey=:col4 and expiry>
                ".time(),
                array(
                    $this->expiry,
                    $this->value,
                    self::$dbinfo['mb_idx'],
                    $key
                )
            );

        } else {
            $this->query(
                "
                update {$this->table("session")}
                set expiry=:col1, value=:col2, regdate=now()
                where sesskey=:col3 and expiry>
                ".time(),
                array(
                    $this->expiry,
                    $this->value,
                    $key
                )
            );
        }
        return true;
    }

    public function destroy($key)
    {
        $this->query(
            "
            delete
            from {$this->table("session")}
            where sesskey=:col1
            ",
            array(
                $key
            )
        );
        return true;
    }

    public function gc(){
        $this->query(
            "
            delete
            from {$this->table("session")}
            where expiry<
            ".time(), ''
        );

        return true;
    }

}

if (SET_SESS_FILE !== true) {
    $handler = new SessionHandler();
    session_set_save_handler(array($handler, 'open'), array($handler, 'close'), array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc'));
    
} else {
    is_dir(PH_SESSION_FILE_PATH) || mkdir(PH_SESSION_FILE_PATH, 0707, true);
    session_save_path(PH_SESSION_FILE_PATH);
}

if (ini_get('session.auto_start') != 1) session_start();
