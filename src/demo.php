<?php
use smartQQ\Core\App;
use smartQQ\Request\Groups;
use smartQQ\Request\Discusses;
require '../vendor/autoload.php';
require 'smartQQ.php';

$app = new App($config);

$app->message->setHandler(function ($msg) {
    print_r($msg);
});

$app->server->server();

$result = (new Discusses())->get();
p($result);

