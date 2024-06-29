<?php

namespace Npds\Support\Facades;

use Npds\Stat\Stat as StatManager;

class Stat
{

    public static function __callStatic($method, $parameters)
    {
        $instance = StatManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}