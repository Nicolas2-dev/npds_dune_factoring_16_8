<?php

namespace Npds\Support\Facades;

use Npds\Utility\Code as CodeManager;

class Code
{

    public static function __callStatic($method, $parameters)
    {
        $instance = CodeManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}