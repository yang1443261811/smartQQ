<?php

namespace smartQQ\Core;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\FileCookieJar;

class Http
{
    protected $client;

    protected $cookieJar;

    public function __construct()
    {
        $this->cookieJar = new FileCookieJar('./cookie.txt', true);
        $this->client = new HttpClient(['cookies' => $this->cookieJar]);
    }

    public function get($uri, $options = array())
    {
        return $this->request($uri, 'GET', $options);
    }

    public function post($url, $options = [], $array = false)
    {
        $content = $this->request($url, 'POST', $options);

        return json_decode($content, true);
    }

    /**
     * @param $url
     * @param string $method
     * @param array  $options
     * @param bool   $retry
     *
     * @return string
     */
    public function request($url, $method = 'GET', $options = [], $retry = false)
    {
        try {
            $options = array_merge(['timeout' => 10, 'verify' => false], $options);

            $response = $this->getClient()->request($method, $url, $options);

            $this->cookieJar->save('./cookie.txt');

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
//            $this->vbot->console->log($url.$e->getMessage(), Console::ERROR, true);

            if (!$retry) {
                return $this->request($url, $method, $options, true);
            }

            return false;
        }
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
        $this->cookieJar = $cookie;
    }

    public function getCookies()
    {
        return $this->cookieJar;
    }
}