<?php
class router {
    protected $flag = 0;

    public static function find($controller, $method){
        $c = $controller.'Controller';
        $obj = new $c;
        $obj -> $method();
        //var_dump($obj);
}
    public function match($url, $ruler){
        //var_dump($ruler);
        foreach($ruler as $left=>$right){
            //echo $left;
            $left = str_replace('/', '\/', $left);
            preg_match($left, $url, $res);
            if($res){
                //var_dump($right);
                //var_dump($res);
                $re_url = explode('?', $right);
                //var_dump($re_url[0]);
                list($controller, $method) = explode('/', $re_url[0]);
                //echo $controller;
                if($re_url[1]){
                    $args = explode('&', $re_url[1]);
                    foreach($args as $arg){
                        $_arg = explode(':', $arg);
                        $_GET[$_arg[0]] = $res[$_arg[1]];
                    }
                }
                //var_dump($_GET);
                self::find($controller, $method);
                $this -> flag += 1;
                break;
            }
        }
        if($this -> flag == 0){
            self::find('basic', 'notFound');
        }
        //var_dump($_GET);
    }
}