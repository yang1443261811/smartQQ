<?php
namespace smartQQ\ServiceProviders;

use smartQQ\Login;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LoginServiceProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['login'] = function ($pimple) {
            return new Login();
        };
    }
}