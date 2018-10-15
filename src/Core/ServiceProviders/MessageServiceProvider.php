<?php

namespace smartQQ\Core\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Core\MessageHandler;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['message'] = function ($pimple) {
            return new MessageHandler($pimple);
        };
    }
}