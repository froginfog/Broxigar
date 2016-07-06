<?php
function smarty_block_cut($params, $content){
    $flag = $params['flag'];
    $num = $params['num'];
    if($flag == true){
        $str = str_replace('，', ',',$content);
        $str = str_replace('。', '.', $str);
    }else{
        $str = $content;
    }
    $res = substr($str, 0, $num);
    return $res;

}