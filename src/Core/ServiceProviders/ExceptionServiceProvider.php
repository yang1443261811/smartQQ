<?php

namespace smartQQ\Core\ServiceProviders;

use Pimple\Container;
use smartQQ\Core\ExceptionHandler;
use Pimple\ServiceProviderInterface;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['exception'] = function ($pimple) {
            return new ExceptionHandler($pimple);
        };
    }
}