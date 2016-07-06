<?php
function autoload($class, $dir=null){
    if(is_null($dir)){
        $dir = __DIR__.'/';
    }
    foreach(scandir($dir) as $file){
        if(is_dir($dir.$file) && substr($file,0,1) != '.'){
            autoload($class, $dir.$file.'/');
        }elseif(is_file($dir.$file) && str_replace('.class.php', null, $file) == $class){
            require_once $dir.$file;
            break;
        }
    }
}

function filter($str){
    if (!get_magic_quotes_gpc ()) {
        $res  =  htmlspecialchars(addslashes($str));
    }
    else {
        $res = htmlspecialchars($str);
    }
    return $res;
}