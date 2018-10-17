<?php

namespace smartQQ\Core\ServiceProviders;

use smartQQ\Core\Http;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['http'] = function ($pimple) {
            return new Http($pimple);
        };
    }
}