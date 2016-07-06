<?php
class DB {
    public static $dbConfig = [];
    public static $link = null;
    public static $flag = false;
    
    public function __construct(){
        global $config;
        self::$dbConfig = [
            'hostname' => $config['DB_HOST'],
            'username' => $config['DB_USER'],
            'password' => $config['DB_PWD'],
            'host'     => $config['DB_HOST'],
            'port'     => $config['DB_PORT'],
            'charset'  => $config['DB_CHARSET'],
            'dsn'      => $config['DB_TYPE'].':host='.$config['DB_HOST'].';dbname='.$config['DB_NAME'].';charset='.$config['DB_CHARSET']
        ];
        if(!self::$flag){
            $dbConfig = self::$dbConfig;
            try{
                self::$link = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);

            }catch (PDOException $e){
                $this->err($e);
            }
            self::$link ->exec('set names'.$dbConfig['charset']);
            self::$flag = true;
        }
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
        return self::$link->quote($str);
    }

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
    }

    public function count($sql){
        $stmt = self::$link->prepare($sql);
        $stmt ->execute();
        return $stmt->fetchColumn();
    }

    public function getAll($sql){
        $stmt = self::$link -> prepare($sql);
        $stmt ->execute();
        $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getOne($sql){
        $stmt = self::$link -> prepare($sql);
        $stmt ->execute();
        $res = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    // insert into table (col1, col2, col3) values ('v1', 'v2', 'v3')
    public function insert($table, $arr){
        $keys = "`". join("`,`", array_keys($arr)) ."`";
        $values = "'". join("','", array_values($arr)) ."'";
        $sql = "insert into $table ($keys) vlaues ($values)";
        $stmt = self::$link -> prepare($sql);
        $stmt -> execute();
        return self::$link -> lastInsertId();
    }

    //update table set col1=v1, col2=v2 where blabla
    public function update($table, $arr, $where){
        $condition = $this->parseCondition($arr);
        $where = " where $where";
        $sql = "update $table set $condition $where";
        $stmt = self::$link -> prepare($sql);
        $stmt -> execute();
        return $stmt->rowCount();
    }

    // delete from table where blabla
    public function delete($table, $arr){
        $where = $this->parseCondition($arr);
        $sql = "delete from $table where $where";
        $stmt = self::$link -> prepare($sql);
        $stmt -> execute();
        return $stmt -> rowCount();
    }

    private function err($err){
        die('错误：'.$err);
    }

    private function close(){
        self::$link = null;
    }
}