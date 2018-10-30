<?php

namespace smartQQ\Request;

/**
 * Class Myself
 * @package smartQQ\Request
 */
class Myself
{
    const URL = 'http://s.web2.qq.com/api/get_self_info2?t=0.1';

    const REFERER = 'http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1';

    /**
     * 获取自己的QQ信息
     *
     * @return mixed
     */
    public function get()
    {
        $options = ['headers' => ['Referer' => self::REFERER]];

        $response = app('http')->get(self::URL, $options);

        return json_decode($response, true);
    }
}