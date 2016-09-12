<?php
class filter {

    /**过滤POST和GET，在需要调用被过滤的POST和GET时使用这个方法
     * @param mixed $key 要在POST和GET中查找的一个或一系列键名
     * @param string $type 指明POST或GET
     * @return mixed $res 返回被过滤后的值
     */
    public static function gp($key, $type=null){
        if(is_array($key)){
            $res = array();
            foreach($key as $k){
                if($type == 'get'){
                    $res[$k] = self::filterEscape($_GET[$k]);
                }
                if($type == 'post'){
                    $res[$k] = self::filterEscape($_POST[$k]);
                }
                if($type == null){
                    if(isset($_GET[$k])){
                        $res[$k] = self::filterEscape($_GET[$k]);
                    }
                    if(isset($_POST[$k])){
                        $res[$k] = self::filterEscape($_POST[$k]);
                    }
                }
            }
            return $res;
        }else{
            $res = '';
            if($type == 'get'){
                $res = self::filterEscape($_GET[$key]);
            }
            if($type == 'post'){
                $res = self::filterEscape($_POST[$key]);
            }
            if($type == null){
                if(isset($_GET[$key])){
                    $res = self::filterEscape($_GET[$key]);
                }
                if(isset($_POST[$key])){
                    $res = self::filterEscape($_POST[$key]);
                }
            }
            return $res;
        }
    }

    /**
     * 过滤字符 防止xss
     * @param $value
     * @return mixed
     */
    public static function filterEscape($value){
        $value = str_replace(array("\0","%00","\r"), '', $value);
        $value = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $value);
        $value = str_replace(array("%3C",'<'), '&lt;', $value);
        $value = str_replace(array("%3E",'>'), '&gt;', $value);
        $value = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $value);
        return $value;
    }
}