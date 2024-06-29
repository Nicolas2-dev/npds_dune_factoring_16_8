<?php

namespace Npds\Support\Facades;

use Npds\Http\HttpProtect as ProtectManager;

class Protect
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ProtectManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}