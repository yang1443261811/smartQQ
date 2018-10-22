<?php

use smartQQ\Core\App;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return App::getInstance();
        }

        return (App::getInstance())->$abstract;
    }
}

if (!function_exists('p')) {

    function p($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

