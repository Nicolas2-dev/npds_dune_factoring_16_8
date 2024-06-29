<?php

namespace Npds\Support\Facades;

use Npds\Cache\SuperCacheManager;

class CacheManager
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SuperCacheManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}