<?php

namespace Npds\Support\Facades;

use Npds\Error\Error as ErrorManager;

class Error
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ErrorManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}