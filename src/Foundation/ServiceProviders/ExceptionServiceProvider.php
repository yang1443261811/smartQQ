<?php

namespace smartQQ\Foundation\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Foundation\ExceptionHandler;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['exception'] = function ($pimple) {
            return new ExceptionHandler($pimple);
        };
    }
}