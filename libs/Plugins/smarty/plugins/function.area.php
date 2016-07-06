<?php
function smarty_function_area($params){
    $w = $params['width'];
    $h = $params['height'];
    return $w * $h;
}