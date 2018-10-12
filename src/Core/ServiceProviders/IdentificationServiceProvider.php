<?php

namespace smartQQ\Core\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Core\Identification;

class IdentificationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['identification'] = function ($pimple) {
            return new Identification($pimple);
        };
    }
}