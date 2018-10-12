<?php

namespace smartQQ\Core;

use GuzzleHttp\Client as HttpClient;

class Http
{
    protected $client;

    protected $cookies;

    public function __construct()
    {
        $this->client = new HttpClient();
    }

    public function get($uri, $options = array())
    {
        $options = array_merge([
            'verify'  => false,
            'cookies' => $this->cookies,
        ], $options);

        return $this->client->request('get', $uri, $options);
    }

    public function post($uri, $options = array())
    {
        $client = self::getClient();

        $options = array_merge([
            'verify'  => false,
            'cookies' => $this->cookies,
        ], $options);

        return $this->client->request('post', $uri, $options);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;
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