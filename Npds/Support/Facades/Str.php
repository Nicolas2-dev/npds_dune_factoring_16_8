<?php

namespace Npds\Support\Facades;

use Npds\Utility\Str as StrManager;

class Str
{

    public static function __callStatic($method, $parameters)
    {
        $instance = StrManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}