<?php
namespace smartQQ\Http;

use GuzzleHttp\Client;

class HttpClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new client();
    }

    public function request($method, $uri = '', array $options = [])
    {
        return $this->client->request($method, $uri, $options);
    }

    public function setClient($httpClient)
    {
        $this->client = $httpClient;

        return $this;
    }

}