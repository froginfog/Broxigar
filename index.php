<?php
require_once './libs/function.php';
spl_autoload_register('autoload');
$config = include 'config.php';
$router = include 'url.php';
session_start();

$start = new router();
$start -> match($_GET['params'], $router);