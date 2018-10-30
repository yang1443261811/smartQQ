<?php

namespace smartQQ\Core;

use smartQQ\Foundation\App;
use smartQQ\Request\PollMessage;
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
            $response = PollMessage::get();

            call_user_func_array($this->handler, [$response]);

            sleep(1);
        }
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