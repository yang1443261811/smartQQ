<?php
namespace smartQQ\Core;

use Pimple\Container;
use Illuminate\Config\Repository;

/**
 * @property \smartQQ\Support\Http $http
 * @property \smartQQ\Core\Credential $credential
 * @property \smartQQ\Core\MessageHandler $message
 * @property \smartQQ\Core\Server $server
 * @property \smartQQ\Core\ExceptionHandler $exception
 * @property \smartQQ\Support\Log $log
 * @property \Illuminate\Config\Repository $config
 */
class App extends Container
{
    protected static $instance;

    protected $providers = [
        ServiceProviders\LogServiceProvider::class,
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\MessageServiceProvider::class,
        ServiceProviders\ExceptionServiceProvider::class,
        ServiceProviders\CredentialServiceProvider::class,
    ];

    public function __construct($config = [])
    {
        $this->initializeConfig($config);

        $this->registerProviders();

        (new Kernel($this))->bootstrap();

        static::$instance = $this;
    }

    private function initializeConfig(array $config)
    {
        $this->config = new Repository($config);
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

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}