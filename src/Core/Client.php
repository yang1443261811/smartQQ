<?php
namespace smartQQ\Core;

use Pimple\Container;
use smartQQ\ServiceProviders\LoginServiceProviders;

class Client extends Container
{
    protected $providers = [
        LoginServiceProviders::class
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    public function __set($id, $value)
    {
        return $this->offsetSet($id, $value);
    }
}