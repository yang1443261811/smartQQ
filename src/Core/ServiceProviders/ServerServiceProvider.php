<?php
namespace smartQQ\Core\ServiceProviders;

use smartQQ\Core\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['server'] = function ($pimple) {
            return new server($pimple);
        };
    }
}