<?php

namespace smartQQ\Core;

use smartQQ\Exception\ArgumentException;

class MessageHandler
{
    protected $app;

    protected $handler;

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
        $options = array('headers' => ['Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2']);

        $result = $this->app->http->post('http://d1.web2.qq.com/channel/poll2', [
            'clientid'   => $this->app->config['clientid'],
            'ptwebqq'    => $this->app->config['server.ptwebqq'],
            'psessionid' => $this->app->config['server.psessionid'],
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