<?php
use smartQQ\Core\Client;
require '../vendor/autoload.php';

$client = new Client();

//$a = $client->identification->toArray();
//print_r($client->identification->toArray());
$client->login->server();

