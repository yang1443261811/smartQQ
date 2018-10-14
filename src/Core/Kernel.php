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

    private function bootstrapException()
    {
//        error_reporting(-1);
//        set_error_handler([$this->vbot->exception, 'handleError']);
//        set_exception_handler([$this->vbot->exception, 'handleException']);
//        register_shutdown_function([$this->vbot->exception, 'handleShutdown']);
    }

    /**
     * initialize config.
     */
    private function initializeConfig()
    {

    }
}