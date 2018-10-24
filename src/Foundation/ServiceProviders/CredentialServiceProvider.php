<?php

namespace smartQQ\Foundation\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Core\Credential;

class CredentialServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['credential'] = function ($pimple) {
            return new Credential($pimple);
        };
    }
}