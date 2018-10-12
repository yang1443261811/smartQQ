<?php

namespace smartQQ\Core\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Core\Identification;

class IdentificationServiceProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['identification'] = function ($pimple) {
            return new Identification($pimple);
        };
    }
}