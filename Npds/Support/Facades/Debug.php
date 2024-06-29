<?php

namespace Npds\Support\Facades;

use Npds\Debug\Debug as DebugManager;

class Debug
{

    public static function __callStatic($method, $parameters)
    {
        $instance = DebugManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}