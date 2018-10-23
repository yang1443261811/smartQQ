<?php

namespace smartQQ\Request;

/**
 * QQ群组
 *
 * Class Groups
 * @package smartQQ\Request
 */
class Groups
{
    const URL = 'http://s.web2.qq.com/api/get_group_name_list_mask2';

    const REFERER = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

    /**
     * 获取QQ群组
     *
     * @return mixed
     */
    public static function get()
    {
        $config = app('config')['server'];

        $options = array('headers' => ['Referer' => self::REFERER]);

        $result = app('http')->post(self::URL, [
            'vfwebqq' => $config['vfwebqq'],
            'hash'    => hashArgs($config['uin'], $config['ptwebqq']),
        ], $options);

        return $result;
    }
}