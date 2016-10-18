<?php
require_once './libs/function.php';
spl_autoload_register('autoload');
$config = include 'config.php';
$router = include 'url.php';
date_default_timezone_set($config['TIMEZONE']);
session_start();

$start = new router();
$start -> match(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO']: "", $router);