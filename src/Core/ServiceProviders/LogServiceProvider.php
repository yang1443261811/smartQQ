<?php

namespace smartQQ\Core\ServiceProviders;

use smartQQ\Support\Log;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['log'] = function ($pimple) {
            return new Log($pimple);
        };
    }
}