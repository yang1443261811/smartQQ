<?php

namespace smartQQ\Request;

/**
 * QQ最新会话记录
 *
 * Class RecentList
 * @package smartQQ\Request
 */
class RecentList
{
    const URL = 'http://d1.web2.qq.com/channel/get_recent_list2';

    const REFERER = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

    /**
     * 获取QQ最新会话记录
     *
     * @return mixed
     */
    public function get()
    {
        $params = [
            'clientid'   => app('config')['clientid'],
            'vfwebqq'    => app('config')['server.vfwebqq'],
            'psessionid' => app('config')['server.psessionid'],
        ];

        $options = ['headers' => ['Referer' => self::REFERER]];

        return app('http')->post(self::URL, $params, $options);
    }
}