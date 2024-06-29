<?php

namespace Npds\Support\Facades;

use Npds\Url\Url as UrlManager;

class Url
{

    public static function __callStatic($method, $parameters)
    {
        $instance = UrlManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}