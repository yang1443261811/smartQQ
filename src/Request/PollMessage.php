<?php

namespace smartQQ\Request;

class PollMessage
{
    const URL = 'http://d1.web2.qq.com/channel/poll2';

    const REFERER = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

    /**
     * 拉取QQ消息
     *
     * @return mixed
     */
    public function get()
    {
        $options = ['headers' => ['Referer' => self::REFERER]];

        $params = [
            'key'        => '',
            'clientid'   => app('config')['clientid'],
            'ptwebqq'    => app('config')['server.ptwebqq'],
            'psessionid' => app('config')['server.psessionid'],
        ];

        return app('http')->post(self::URL, $params, $options);
    }
}