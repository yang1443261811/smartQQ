<?php
namespace smartQQ\Core;

use Pimple\Container;

class Client extends Container
{
    protected $providers = [
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\LoginServiceProvider::class,
        ServiceProviders\IdentificationServiceProvider::class
    ];

    public function __construct()
    {
        $this->registerProviders();

        (new Kernel($this))->bootstrap();
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