<?php

namespace Npds\Support\Facades;

use Npds\Cookie\Cookie as CookieManager;

class Cookie
{

    public static function __callStatic($method, $parameters)
    {
        $instance = CookieManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}