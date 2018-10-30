<?php

namespace smartQQ\Foundation\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use smartQQ\Request\Friends;
use smartQQ\Request\Groups;
use smartQQ\Request\Myself;
use smartQQ\Request\Discusses;
use smartQQ\Request\RecentList;
use smartQQ\Request\PollMessage;

class RequestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['discusses'] = function ($pimple) {
            return new Discusses();
        };

        $pimple['friends'] = function ($pimple) {
            return new Friends();
        };

        $pimple['groups'] = function ($pimple) {
            return new Groups();
        };

        $pimple['myself'] = function ($pimple) {
            return new Myself();
        };

        $pimple['recentList'] = function ($pimple) {
            return new RecentList();
        };

        $pimple['message'] = function ($pimple) {
            return new PollMessage();
        };
    }
}