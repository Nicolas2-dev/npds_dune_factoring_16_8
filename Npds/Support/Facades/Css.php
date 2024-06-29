<?php

namespace Npds\Support\Facades;

use Npds\Asset\Css as CssManager;

class Css
{

    public static function __callStatic($method, $parameters)
    {
        $instance = CssManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}