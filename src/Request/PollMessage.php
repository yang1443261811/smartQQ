<?php

namespace smartQQ\Request;

use smartQQ\Exception\ArgumentException;

class PollMessage
{
    protected $handler;

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

    /**
     * 循环监听QQ消息
     *
     * @return void
     */
    public function listen()
    {
        while (true) {
            $response = $this->get();

            call_user_func_array($this->handler, [$response]);

            sleep(1);
        }
    }

    /**
     * 自定义QQ消息处理函数
     *
     * @param $callback
     * @throws ArgumentException
     */
    public function setHandler($callback)
    {
        if (!is_callable($callback)) {
            throw new ArgumentException('Argument must be callable in ' . get_class());
        }

        $this->handler = $callback;
    }

    public function handleMessage($text)
    {

    }
}