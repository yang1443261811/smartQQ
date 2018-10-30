<?php

namespace smartQQ\Request;

/**
 * QQ好友
 *
 * Class Friends
 * @package smartQQ\Request
 */
class Friends
{
    const URL = 'http://s.web2.qq.com/api/get_user_friends2';

    const REFERER = 'http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1';

    /**
     * 获取QQ好友
     *
     * @return mixed
     */
    public function get()
    {
        $params = [
            'vfwebqq' => app('config')['server.vfwebqq'],
            'hash'    => hashArgs(app('config')['server.uin'], app('config')['server.ptwebqq'])
        ];

        $options = ['headers' => ['Referer' => self::REFERER]];

        return app('http')->post(self::URL, $params, $options);
    }
}