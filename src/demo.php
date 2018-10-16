<?php
use smartQQ\Core\Client;

require '../vendor/autoload.php';
require 'smartQQ.php';

$client = new Client($config);

//$a = $client->identification->toArray();
//print_r($client->identification->toArray());

$client->message->setHandler(function ($msg) {
    print_r($msg);
});

$client->server->server();

