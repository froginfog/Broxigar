<?php
return array(
    /**
     *   '/^user\/(\d+)\/(\w+)$/'  =>  'index/user?id:1&name:2'
     *   正则表达式                 =>   '控制器/方法 (? 参数1:匹配值1 & 参数2:匹配值2)'
    **/
    '/^$/' => 'index/index',
    '/^user\/(\d+)\/(\w+)$/'  =>  'index/user?id:1&name:2',
);