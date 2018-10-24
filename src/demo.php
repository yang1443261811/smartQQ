<?php
use smartQQ\Foundation\App;
use smartQQ\Request\Groups;
use smartQQ\Request\Discusses;
use smartQQ\Request\Friends;
use smartQQ\Request\RecentList;
use smartQQ\Request\Myself;
require '../vendor/autoload.php';
require 'smartQQ.php';

$app = new App($config);

$app->message->setHandler(function ($msg) {
    print_r($msg);
});

$app->server->server();

$data = Myself::get();
p($data);

//$friend = $data['result']['friends'][0];
//$marknames = $data['result']['marknames'][0];
//$categories = $data['result']['categories'][0];
//$vipinfo = $data['result']['vipinfo'][0];
//$info = $data['result']['info'][0];
//print_r($friend);
//echo '</br>';
//print_r($marknames);
//echo '</br>';
//print_r($categories);
//echo '</br>';
//print_r($vipinfo);
//echo '</br>';
//print_r($info);
//echo count($result);die;
//foreach ($result as $item) {
//
//}

