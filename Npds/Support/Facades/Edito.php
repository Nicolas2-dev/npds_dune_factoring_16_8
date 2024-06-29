<?php

namespace Npds\Support\Facades;

use Npds\Edito\Edito as EditoManager;

class Edito
{

    public static function __callStatic($method, $parameters)
    {
        $instance = EditoManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}