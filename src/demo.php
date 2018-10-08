<?php
use smartQQ\Http\HttpClient;
use smartQQ\Console\QrCode;

header("Content-type: image/jpeg");

require '../vendor/autoload.php';
$http = new HttpClient();

(new QrCode)->show('https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=2&l=M&s=3&d=72&v=4&t=0.8310633005603156&daid=164&pt_3rd_aid=0');
return;

$response = $http->request('get', 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=2&l=M&s=3&d=72&v=4&t=0.8310633005603156&daid=164&pt_3rd_aid=0', ['verify' => false]);
echo $response->getBody();
