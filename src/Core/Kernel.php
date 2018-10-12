<?php

namespace smartQQ\Core;

class Kernel
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;

    }

    public function bootstrap()
    {
    }
}