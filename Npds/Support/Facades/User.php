<?php

namespace Npds\Support\Facades;

use Npds\Auth\User as UserManager;

class User
{

    public static function __callStatic($method, $parameters)
    {
        $instance = UserManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}