<?php

namespace smartQQ\Core;

use Throwable;

class ExceptionHandler
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handleError($level, $message, $file = '', $line = 0)
    {
        echo 'error';
    }

    public function handleException(Throwable $e)
    {
        echo $e->getMessage();
        $this->app->log->error($e->getMessage());
    }

    public function handleShutdown()
    {

    }
}