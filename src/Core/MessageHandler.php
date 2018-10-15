<?php

namespace smartQQ\Core;

use smartQQ\Exception\ArgumentException;

class MessageHandler
{
    protected $client;

    protected $handler;

    protected $uri = 'http://d1.web2.qq.com/channel/poll2';

    protected $referer = 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2';

    public function __construct(Client $client)
    {
        $this->client = $client;
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
            'ptwebqq'    => $this->client->identification->getPtWebQQ(),
            'clientid'   => $this->client->identification->getClientId(),
            'psessionid' => $this->client->identification->getPSessionId(),
            'key'        => '',
        ]);
        $options = array(
            'form_params' => $params,
            'headers'     => [ 'Referer' => $this->referer]
        );
        $this->client->http->setCookies($this->client->identification->getCookies());
        $body = $this->client->http->post($this->uri, $options)->getBody();
        return $body ? json_decode($body, true) : '';
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