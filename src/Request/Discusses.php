<?php

namespace smartQQ\Request;

/**
 * QQ讨论组
 *
 * Class Discusses
 * @package smartQQ\Request
 */
class Discusses
{
    /**
     * 获取QQ讨论组
     *
     * @return array|mixed|object|\stdClass
     */
    public static function get()
    {
        $config = app('config')['server'];
        $url = sprintf(
            'http://s.web2.qq.com/api/get_discus_list?clientid=53999199&psessionid=%s&vfwebqq=%s&t=0.1',
            $config['psessionid'],
            $config['vfwebqq']
        );

        $response = app('http')->get($url, ['headers' =>
            ['Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2']
        ]);

        return json_decode($response, true);
    }
}