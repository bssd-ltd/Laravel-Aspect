<?php

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $make
     * @param array $parameters
     * @return mixed|Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}
