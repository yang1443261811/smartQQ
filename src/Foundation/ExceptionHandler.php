<?php

namespace smartQQ\Foundation;

use Throwable;
use ErrorException;

class ExceptionHandler
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException(Throwable $e)
    {
        $this->app->log->error($e->getMessage() . ' ' . $e->getFile() . ' on line ' . $e->getLine());

        throw $e;
    }

    public function handleShutdown()
    {

    }
}