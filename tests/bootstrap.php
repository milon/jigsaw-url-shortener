<?php

use Illuminate\Container\Container;

require dirname(__DIR__) . '/vendor/autoload.php';

if (! function_exists('app')) {
    function app(?string $abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}
