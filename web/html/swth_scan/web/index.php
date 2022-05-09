<?php

// comment out the following two lines when deployed to production

$debug = in_array(getUserIP(),['109.252.93.47'])?true:false;

defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_ENV') or define('YII_ENV', $debug?'dev':'production');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();



function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP))
    {$ip = $client;}
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {$ip = $forward;}
    else {$ip = $remote;}
    return $ip;
}