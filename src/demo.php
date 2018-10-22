<?php
use smartQQ\Core\App;
use smartQQ\Request\Groups;
require '../vendor/autoload.php';
require 'smartQQ.php';

$app = new App($config);


$app->message->setHandler(function ($msg) {
    print_r($msg);
});

$app->server->server();

$result = (new Groups())->get();
p($result);

//echo app('config')['credential_file'];
