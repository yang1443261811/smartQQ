<?php
$path = './Storage/';

$config = array(
    'path'              => $path,
    'credential_file'   => './credential.json',
    'cookie_file'       => './cookie.txt',
    'clientid'          => 53999199,

    /**
     * 日志配置项
     */
    'log'      => [
        'level'         => 'debug',
        'permission'    => 0777,
        'system'        => $path.'logs/log.txt', // 系统报错日志
        'message'       => $path.'logs/log.txt', // 消息日志
    ],
);

