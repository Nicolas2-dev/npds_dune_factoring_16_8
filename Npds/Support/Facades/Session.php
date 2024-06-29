<?php

namespace Npds\Support\Facades;

use Npds\Session\Session as SessionManager;

class Session
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SessionManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}