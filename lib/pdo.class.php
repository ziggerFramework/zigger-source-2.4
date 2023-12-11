<?php
namespace Make\Database;

use Corelib\Func;

class Pdosql {

    static private $DB_HOST = DB_HOST;
    static private $DB_NAME = DB_NAME;
    static private $DB_USER = DB_USER;
    static private $DB_PWD = DB_PWD;
    static private $DB_PREFIX = DB_PREFIX;
    static private $ALREADY_CONNECTED_PDO;
    private $ROW = 0;
    private $ROW_RE;
    private $ROW_NUM = 0;
    private $REC_COUNT;
    private $pdo;
    private $stmt;
    private $dsn;
    private $options;
    public $specialchars;
    public $nl2br;

    // pdo 연결 초기화
    public function __construct()
    {
        try {
            $this->dsn = 'mysql:host='.self::$DB_HOST.';dbname='.self::$DB_NAME;
            $this->options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            );

            if (!empty(self::$ALREADY_CONNECTED_PDO)) {
                $this->pdo = self::$ALREADY_CONNECTED_PDO;
                
            } else {
                $this->pdo = new \PDO($this->dsn, self::$DB_USER, self::$DB_PWD, $this->options);
                self::$ALREADY_CONNECTED_PDO = $this->pdo;
            }
            

        } catch (\PDOException $e) {
            Func::core_err(ERR_MSG_3 . '<br />' . $e->getMessage());
        }

        $this->specialchars = DB_SPECIALCHARS;
        $this->nl2br = DB_NL2BR;
    }

    // pdo 연결 종료
    public function close()
    {
        $this->pdo = null;
    }

    // 테이블 명칭 조합 후 반환
    public function table_exists($tblName)
    {
        $this->query(
            "
            select count(*) booleans
            from Information_schema.tables
            where table_schema = '".self::$DB_NAME."'
            and table_name = '".$this->table($tblName)."'
            "
        );

        return $this->fetch('booleans');
    }

    // 문자열 password 처리
    public function set_password($str)
    {
        $str = addslashes($str);
        $str = "concat('*', upper(sha1(unhex(sha1('$str')))))";
        return $str;
    }

    // 테이블 명칭 조합 후 반환
    public function table($tblName)
    {
        $expl = explode(':', $tblName);
        $tbl = '';

        // 모듈 Table인 경우
        if (count($expl) > 1) {
            $tbl = ($expl[1]) ? self::$DB_PREFIX.$expl[0].'_'.$expl[1] : self::$DB_PREFIX.$expl[0];
        }

        // 그 외 기본 Table
        else {
            $tbl = self::$DB_PREFIX.$tblName;
        }

        return $tbl;
    }

    // Query
    public function query($query, $param = [], $dspError = true)
    {
        try {

            $qryString = $query;

            $this->stmt = $this->pdo->prepare($query);

            if (is_array($param)) {
                for ($i = 1; $i <= count($param); $i++) {
                    if (!strstr($query, ':col'.$i)) continue;
                    
                    $value = addslashes(isset($param[$i-1]) ? $param[$i-1] : '');

                    if (is_null($param[$i-1])) {
                        $this->stmt->bindValue(':col'.$i, null, \PDO::PARAM_NULL);
                    } else {
                        $this->stmt->bindValue(':col'.$i, $value, \PDO::PARAM_STR);
                    }

                    $qryString = str_replace(':col'.$i, (is_null($value) || is_numeric($value)) ? $value : "'$value'", $qryString);
                }
            }

            $this->stmt->execute();
            $this->REC_COUNT = $this->stmt->rowCount();

            $qryLower = strtolower($query);

            if ( strpos($qryLower, 'select') !== false && ( strpos($qryLower, 'insert') === false && strpos($qryLower, 'update') === false ) ) {
                $this->ROW = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            }
            $this->ROW_NUM = 1;

            return $qryString;

        }
        catch (\PDOException $e) {
            if ($dspError === true) {
                Func::core_err(ERR_MSG_5.'<br />'.$e->getMessage());

            } else {
                return false;
            }
        }
    }

    // 레코드의 개수를 구함
    public function getcount()
    {
        return $this->REC_COUNT;
    }

    // 다음 레코드에 위치 시킴
    public function nextRec()
    {
        $this->ROW_NUM = $this->ROW_NUM + 1;

        if ($this->ROW_NUM <= $this->REC_COUNT) {
            $this->ROW = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            return true;

        } else {
            return false;
        }
    }

    // 레코드의 특정 필드 값을 가져옴
    public function fetch($fieldName)
    {
        if (isset($this->ROW[$fieldName])) {
            $this->ROW_RE = stripslashes($this->ROW[$fieldName]);
            if ($this->specialchars == 1) $this->ROW_RE = htmlspecialchars($this->ROW_RE);
            if ($this->nl2br == 1) $this->ROW_RE = nl2br($this->ROW_RE);
            $this->ROW_RE = preg_replace(['/[\x00-\x08]/', '/\x0B-\x1F/'], [' ', ' '], $this->ROW_RE); // 입력된 Ascii 특수 문자를 공백으로 치환 (0x00 ~ 0x1F)

            return $this->ROW_RE;

        } else {
            return '';
        }
    }

    // 레코드의 모든 필드 값을 배열로 가져옴
    public function fetchs()
    {
        $array = array();

        if (!$this->ROW) return false;

        foreach ($this->ROW as $key => $value) {
            $array[$key] = $this->fetch($key);
        }

        return $array;
    }

    // 여분필드 설명 처리
    public function etcfd_exp($exp)
    {
        $ex = explode('{|}', $exp);

        for ($i = 0; $i < 10; $i++) {
            if (!isset($ex[$i])) $ex[$i] = '';
            $ex[$i] = str_replace('|', '&vert;', $ex[$i]);
        }
        
        return implode('|', $ex);
    }
}
