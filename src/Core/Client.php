<?php
namespace smartQQ\Core;

use Pimple\Container;

/**
 * @property \smartQQ\Core\Http $http
 * @property \smartQQ\Core\Credential $credential
 * @property \smartQQ\Core\MessageHandler $message
 * @property \smartQQ\Core\Server $server
 */
class Client extends Container
{
    protected $providers = [
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\MessageServiceProvider::class,
        ServiceProviders\CredentialServiceProvider::class
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