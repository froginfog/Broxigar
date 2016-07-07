<?php
class DB {
    private $dbConfig = [];
    private $link = null;
    private static $instance = null;

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone() {}

    private function __construct(){
        global $config;
        $this->dbConfig = [
            'hostname' => $config['DB_HOST'],
            'username' => $config['DB_USER'],
            'password' => $config['DB_PWD'],
            'host'     => $config['DB_HOST'],
            'port'     => $config['DB_PORT'],
            'charset'  => $config['DB_CHARSET'],
            'dsn'      => $config['DB_TYPE'].':host='.$config['DB_HOST'].';dbname='.$config['DB_NAME'].';charset='.$config['DB_CHARSET']
        ];
        $dbConfig = $this->dbConfig;
        try {
            $this->link = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            $this->err($e);
        }
        $this->link->exec('set names ' . $dbConfig['charset']);
    }

    public function __destruct(){
        self::close();
    }

    /**
     * 为SQL语句中的字符串添加引号
     * @param $str
     * @return string
     */
    public function quote($str){
        return $this->link->quote($str);
    }
    /*
    private function parseCondition($arr){
        $str = "";
        foreach($arr as $k=>$v){
            if($str == ""){
                $sep = "";
            }else{
                $sep = ",";
            }
            $str .= $sep."`".$k."`"."='".$v."'";
        }
        return $str;
    }*/
    /**
     * 为bindValue生成带占位符的字符串
     * @param $arr
     * @return string
     */
    private function parseCondition($arr){
        $str = "";
        foreach($arr as $k=>$v){
            if($str == ""){
                $sep = "";
            }else{
                $sep = ",";
            }
            $str .= $sep."`".$k."`"."=:".$k;
        }
        return $str;
    }

    public function count($sql){
        $stmt = $this->link->prepare($sql);
        $stmt ->execute();
        return $stmt->fetchColumn();
    }

    public function getAll($sql){
        $stmt = $this->link -> prepare($sql);
        $stmt ->execute();
        $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getOne($sql){
        $stmt = $this->link -> prepare($sql);
        $stmt ->execute();
        $res = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    /**
     * insert into table (col1, col2, col3) values ('v1', 'v2', 'v3')
     * @param string $table
     * @param array $arr
     */
    public function insert($table, $arr){
        $keys = "`". join("`,`", array_keys($arr)) ."`";
        //$values = "'". join("','", array_values($arr)) ."'";
        $placeHolder = '';
        foreach ($arr as $k=>$v){
            if($placeHolder == ''){
                $sep = ':';
            }else{
                $sep = ',:';
            }
            $placeHolder .= $sep.$k;
        }
        $sql = "insert into `$table` ($keys) values ($placeHolder)";
        $stmt = $this->link -> prepare($sql);
        foreach($arr as $key=>$value){
            $stmt->bindValue(':'.$key, $value);
        }
        $stmt -> execute();
        //return $this->link -> lastInsertId();
    }

    /**
     * update table set col1=v1, col2=v2 where blabla
     * @param string $table
     * @param array $arr
     * @param array $where
     * @return int
     */
    public function update($table, $arr, $where){
        $condition = $this->parseCondition($arr);
        $_where = $this->parseCondition($where);
        $_where = ' where '.$_where;
        $bind = array_merge($arr, $where);
        $sql = "update `$table` set $condition $_where";
        $stmt = $this->link -> prepare($sql);
        foreach($bind as $key=>$value){
            $stmt->bindValue(':'.$key, $value);
        }
        $stmt -> execute();
        return $stmt->rowCount();
    }

    /**
     * delete from table where blabla
     * @param string $table
     * @param array $arr
     * @return int
     */
    public function delete($table, $arr){
        $where = $this->parseCondition($arr);
        $sql = "delete from $table where $where";
        $stmt = $this->link -> prepare($sql);
        foreach($arr as $key=>$value){
            $stmt->bindValue(':'.$key, $value);
        }
        $stmt -> execute();
        return $stmt -> rowCount();
    }

    /**
     * 执行复杂的update、insert、delete时可以调用这个方法。
     * @param string $sql
     * @return int
     */
    public function doSql($sql){
        $count = $this->link -> exec($sql);
        return $count;
    }

    public function lastInsertId(){
        return $this->link->lastInsertId();
    }

    private function err($err){
        die('错误：'.$err);
    }

    private function close(){
        $this->link = null;
    }
}