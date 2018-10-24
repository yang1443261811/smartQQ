<?php

namespace smartQQ\Foundation\ServiceProviders;

use smartQQ\Support\Http;
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