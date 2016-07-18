<?php
function smarty_function_url($params){
    global $config;
    return $config['ROOT'].implode('/',$params);
}