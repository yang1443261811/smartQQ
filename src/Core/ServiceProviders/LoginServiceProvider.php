<?php
namespace smartQQ\Core\ServiceProviders;

use smartQQ\Login;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LoginServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['login'] = function ($pimple) {
            return new Login($pimple);
        };
    }
}