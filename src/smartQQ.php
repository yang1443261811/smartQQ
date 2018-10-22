<?php
$path = './tmp/';

$config = array(
    'path'              => $path,
    'credential_file'   => './credential.php',
    'cookie_file'       => './cookie.txt',
    'clientid'          => 53999199,

    /*
       * 日志配置项
       */
    'log'      => [
        'level'         => 'debug',
        'permission'    => 0777,
        'system'        => $path.'log.txt', // 系统报错日志
        'message'       => $path.'log.txt', // 消息日志
    ],
);

