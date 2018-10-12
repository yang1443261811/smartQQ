<?php
namespace smartQQ\Core\ServiceProviders;

use smartQQ\Login;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LoginServiceProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['login'] = function ($pimple) {
            return new Login($pimple);
        };
    }
}