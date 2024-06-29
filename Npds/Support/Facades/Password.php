<?php

namespace Npds\Support\Facades;

use Npds\Password\Password as PasswordManager;

class Password
{

    public static function __callStatic($method, $parameters)
    {
        $instance = PasswordManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}