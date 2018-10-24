<?php

namespace smartQQ\Support;

use smartQQ\Foundation\App;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\FileCookieJar;

class Http
{

    protected $app;

    protected $client;

    protected $cookieJar;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->cookieJar = new FileCookieJar($this->app->config['cookie_file'], true);
        $this->client = new HttpClient(['cookies' => $this->cookieJar]);
    }

    public function get($uri, $options = array())
    {
        return $this->request($uri, 'GET', $options);
    }

    public function post($uri, $params = [], $options = [])
    {
        $data = array('form_params' => ['r' => json_encode($params)]);
        $options = array_merge($data, $options);

        $content = $this->request($uri, 'POST', $options);

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
            $options = array_merge(['verify' => false], $options);

            $response = $this->getClient()->request($method, $url, $options);

            $this->cookieJar->save($this->app->config['cookie_file']);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            $this->app->log->debug($url.$e->getMessage());

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