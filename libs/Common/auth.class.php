<?php
class auth{
    //数据库查询时使用的字段名
    private $select_table;
    private $select_uid;
    private $select_uname;
    private $select_upwd;
    private $select_uaccesslevel;

    //cookie设置
    private $allow_cookie = false; //是否允许通过cookie登录
    private $cookie_expire;
    private $cookie_where;
    private $cookie_domain;
    private $cookie_secure;
    private $cookie_http;

    private $uid;
    private $uname;
    private $pwd;
    private $access_level;

    private $key = 'vurtne';

    /**
     * auth constructor.
     * @param array $config
     */
    public function __construct($config=null){
        $this->select_table = $config['select_table'] ? $config['select_table'] : 'user';
        $this->select_uid = $config['select_uid'] ? $config['select_uid'] : 'uid';
        $this->select_uname = $config['select_uname'] ? $config['select_uname'] : 'username';
        $this->select_upwd = $config['select_upwd'] ? $config['select_upwd'] : 'password';
        $this->select_uaccesslevel = $config['select_uaccesslevel'] ? $config['select_uaccesslevel'] : 'access_level';
    }

    /**
     * 调用以允许cookie登录
     * @param array $config
     */
    public function allowCookie($config){
        $this->allow_cookie = true;
        $this->cookie_expire = $config['cookie_expire'];
        $this->cookie_where = $config['cookie_where'];
        $this->cookie_domain = $config['cookie_domain'];
        $this->cookie_secure = $config['cookie_secure'];
        $this->cookie_http = $config['cookie_http'];
    }

    /**
     * 登录
     * @param string $_username
     * @param string $_pwd
     * @return bool
     */
    public function login($_username, $_pwd){
        $sql = 'select '.$this->select_uid.','.$this->select_uaccesslevel.' from '.$this->select_table.' where '.$this->select_uname.'=? and '.$this->select_upwd.'=?';
        $arr = array(
            array('value'=>$_username, 'type'=>PDO::PARAM_STR),
            array('value'=>$_pwd, 'type'=>PDO::PARAM_STR)
        );
        try{
            $db = DB::getInstance();
            $res = $db->prepare($sql)->bindValue($arr)->execute()->getOne();
        }catch(PDOException $e){
            exit($e->getMessage());
        }
        if($res){
            $this->uid = $this->encode($res[$this->select_uid]);
            $this->access_level = $this->encode($res[$this->select_uaccesslevel]);
            $this->uname = $this->encode($_username);
            $this->pwd = $this->encode($_pwd);
            $_SESSION['uid'] = $this->uid;
            $_SESSION['username'] = $this->uname;
            $_SESSION['pwd'] = $this->pwd;
            $_SESSION['access_level'] = $this->access_level;
            if($this->allow_cookie){
                setcookie('username', $this->uname, time()+$this->cookie_expire, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
                setcookie('password', $this->pwd, time()+$this->cookie_expire, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
                setcookie('uid', $this->uid, time()+$this->cookie_expire, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
                setcookie('access_level', $this->access_level, time()+$this->cookie_expire, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * 登出
     */
    public function logout(){
        $this->uid = null;
        $this->access_level = null;
        $this->uname = 'guest';
        $this->pwd = null;
        $_SESSION['uid'] = null;
        $_SESSION['username'] = 'guest';
        $_SESSION['pwd'] = null;
        $_SESSION['access_level'] = null;
        if($this->allow_cookie){
            setcookie('username', 'guest', time()-1, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
            setcookie('password', null, time()-1, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
            setcookie('uid', null, time()-1, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
            setcookie('access_level', null, time()-1, $this->cookie_where, $this->cookie_domain, $this->cookie_secure, $this->cookie_http);
        }
    }

    /**
     * 读取session cookie判断能否登录
     */
    public function init(){
        $this->uid = null;
        $this->access_level = null;
        $this->uname = 'guest';
        $this->pwd = null;
        if($this->checkSession()){
            return true;
        }else{
            return $this->checkCookie();
        }
    }


    private function checkSession(){
        if(!is_null($_SESSION['uid'])){
            return $this->check($this->decode($_SESSION['username']), $this->decode($_SESSION['pwd']));
        }else{
            return false;
        }
    }

    private function checkCookie(){
        if($this->allow_cookie && !is_null($_COOKIE['uid'])){
            return $this->check($this->decode($_COOKIE['username']), $this->decode($_COOKIE['password']));
        }else{
            return false;
        }
    }

    private function check($username, $pwd){
        $sql = 'select '.$this->select_uid.','.$this->select_uaccesslevel.','.$this->select_upwd.' from '.$this->select_table.' where '.$this->select_uname.'=?';
        $arr = array(
            array('value'=>$username, 'type'=>PDO::PARAM_STR)
        );
        try{
            $db = DB::getInstance();
            $res = $db->prepare($sql)->bindValue($arr)->execute()->getOne();
        }catch(PDOException $e){
            exit($e->getMessage());
        }
        if($res[$this->select_upwd] == $pwd){
            $this->uid = $this->encode($res[$this->select_uid]);
            $this->access_level = $this->encode($res[$this->select_uaccesslevel]);
            $this->uname = $this->encode($username);
            $this->pwd = $this->encode($pwd);
            return true;
        }else{
            return false;
        }
    }

    public function encode($str){
        $str = (string)$str;
        $encrypt_key = md5(mt_rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($str); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode($this->keying($tmp, $this->key));
    }

    public function decode($str){
        $txt = $this->keying(base64_decode($str), $this->key);
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $tmp .= $txt[$i] ^ $txt[++$i];
        }
        return $tmp;
    }

    private function keying($str, $key){
        $encrypt_key = md5($key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($str); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $str[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }
}