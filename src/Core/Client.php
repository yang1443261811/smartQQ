<?php
namespace smartQQ\Core;

use Pimple\Container;
use smartQQ\Core\ServiceProviders\LoginServiceProviders;

class Client extends Container
{
    protected $providers = [
        LoginServiceProviders::class
    ];

    public function __construct()
    {
        parent::__construct();

        $this->registerProviders();
    }

    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
//            echo  LoginServiceProviders::class;die;
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