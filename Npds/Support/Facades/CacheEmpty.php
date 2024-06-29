<?php

namespace Npds\Support\Facades;

use Npds\Cache\SuperCacheEmpty;

class CacheEmpty
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SuperCacheEmpty::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}