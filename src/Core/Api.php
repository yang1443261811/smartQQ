<?php

namespace smartQQ\Core;

use GuzzleHttp\Client;

class Api
{
    protected static $client = null;

    protected $cookies;

    public function get($uri, $options = array())
    {
        $client = self::getClient();

        $options = array_merge([
            'verify'  => false,
            'cookies' => $this->cookies,
        ], $options);

        return $client->request('get', $uri, $options);
    }

    protected static function getClient()
    {
        if (self::$client) {
            return self::$client;
        }

        return self::$client = new Client(['cookies' => true]);
    }

    public function setCookies($cookie)
    {
        $this->cookies = $cookie;
    }

    public function getCookies()
    {
        return $this->cookies;
    }
}