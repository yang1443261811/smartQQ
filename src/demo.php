<?php
use smartQQ\Core\App;

require '../vendor/autoload.php';
require 'smartQQ.php';

$app = new App($config);


$app->message->setHandler(function ($msg) {
    print_r($msg);
});

$app->server->server();

//echo app('config')['credential_file'];
