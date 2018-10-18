<?php

namespace smartQQ\Core;

use Monolog\Handler\StreamHandler;

class Kernel
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;

    }

    public function bootstrap()
    {
        $this->setLog();
        $this->bootstrapException();
    }

    private function bootstrapException()
    {
        error_reporting(-1);
        set_error_handler([$this->app->exception, 'handleError']);
        set_exception_handler([$this->app->exception, 'handleException']);
        register_shutdown_function([$this->app->exception, 'handleShutdown']);
    }

    /**
     * initialize config.
     */
    private function initializeConfig()
    {

    }

    private function setLog()
    {
        $config = $this->app->config['log'];

        $this->app->log->pushHandler(new StreamHandler(
            $config['system'],
            $config['level'],
            true,
            $config['permission']
        ));

    }
}