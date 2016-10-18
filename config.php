<?php
return array(
    //时区设置
    'TIMEZONE'            => 'Asia/Shanghai',
    //网站所在目录
    'ROOT'                => '/jisuan/',
    //上传文件存放位置
    'UPLOAD'              => './uploads',

    //smarty配置
    'SM_LEFT_DELIMITER'   => '{',
    'SM_RIGHT_DELIMITER'  => '}',
    'TEMPLATE_DIR'        => './templates/tpl',
    'COMPILE_DIR'         => './templates/tpl_c',
    'CAHCE_DIR'           => './templates/cache',
    'CACHEING'            => false,
    'CACHE_LIFETIME'      => 120,
    'STATICROOT'          => '',

    //mysql配置
    'DB_HOST'             => 'localhost',
    'DB_USER'             => 'root',
    'DB_PWD'              => '',
    'DB_NAME'             => 'jisuan',
    'DB_PORT'             => '3306',
    'DB_TYPE'             => 'mysql',
    'DB_CHARSET'          => 'utf8',

    //水印图片存放位置
    'watermark'           => './static/images/shuiyin.png',

    //后台产品列表每页个数
    'adminProPageSize'    => 20,
);