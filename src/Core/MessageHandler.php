<?php

namespace smartQQ\Core;

use smartQQ\Exception\ArgumentException;

class MessageHandler
{
    protected $app;

    protected $handler;

    protected $uri = 'http://d1.web2.qq.com/channel/poll2';

    protected $referer = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

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
        $params['r'] = json_encode([
            'ptwebqq'    => $this->app->config['ptwebqq'],
            'clientid'   => $this->app->config['clientid'],
            'psessionid' => $this->app->config['psessionid'],
            'key'        => '',
        ]);

        $result = $this->app->http->post($this->uri, [
            'form_params' => $params,
            'headers'     => ['Referer' => $this->referer]
        ]);

        return $result;
    }

    public function handleMessage($text)
    {

    }

    public function setHandler($callback)
    {
        if (!is_callable($callback)) {
            throw new ArgumentException('Argument must be callable in '.get_class());
        }

        $this->handler = $callback;
    }
}