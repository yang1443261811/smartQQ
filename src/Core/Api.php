<?php

namespace smartQQ\Core;

use GuzzleHttp\Client;

class Api
{
    protected static $client = null;

    protected $cookies;

    /**
     * send a curl request
     *
     * @param $api
     * @param $data
     * @param string $method
     * @return mixed
     */
    public static function request($api, $data, $method = 'post')
    {
        $client = self::getClient();

        $options = [
            'verify'  => false,
            'timeout' => 15,
        ];

        $options = is_array($data) ? array_merge($options, ['form_params' => $data]) : array_merge($options, ['body' => $data]);

        $request = ($method === 'post') ? $client->post($api, $options) : $client->get($api);

        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function makeRequest($method, $uri, $options = array())
    {
        $client = self::getClient();

        $options = array_merge([
            'verify'  => false,
            'cookies' => $this->cookies,
        ], $options);

        return $client->request($method, $uri, $options);
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