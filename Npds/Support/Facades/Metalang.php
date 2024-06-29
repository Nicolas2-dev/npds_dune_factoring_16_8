<?php

namespace Npds\Support\Facades;

use Npds\Metalang\Metalang as MetalangManager;

class Metalang
{

    public static function __callStatic($method, $parameters)
    {
        $instance = MetalangManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}