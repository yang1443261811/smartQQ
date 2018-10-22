<?php

namespace smartQQ\Core;

use smartQQ\Exception\ArgumentException;

class MessageHandler
{
    protected $app;

    protected $handler;

    const URL = 'http://d1.web2.qq.com/channel/poll2';

    const REFERER = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function listen()
    {
        while (true) {
            $response = $this->pollMessage();

            call_user_func_array($this->handler, [$response]);

            sleep(1);
        }
    }

    public function pollMessage()
    {
        $config = app('config')['server'];
        $options = array('headers' => ['Referer' => self::REFERER]);

        $result = app('http')->post(self::URL, [
            'clientid'   => $config['clientid'],
            'ptwebqq'    => $config['server.ptwebqq'],
            'psessionid' => $config['server.psessionid'],
            'key' => '',
        ], $options);

        return $result;
    }

    public function handleMessage($text)
    {

    }

    public function setHandler($callback)
    {
        if (!is_callable($callback)) {
            throw new ArgumentException('Argument must be callable in ' . get_class());
        }

        $this->handler = $callback;
    }
}