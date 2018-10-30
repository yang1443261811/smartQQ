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
    public function get()
    {
        $url = sprintf(
            'http://s.web2.qq.com/api/get_discus_list?clientid=53999199&psessionid=%s&vfwebqq=%s&t=0.1',
            app('config')['server.psessionid'],
            app('config')['server.vfwebqq']
        );

        $options = ['Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2'];

        $response = app('http')->get($url, $options);

        return json_decode($response, true);
    }
}