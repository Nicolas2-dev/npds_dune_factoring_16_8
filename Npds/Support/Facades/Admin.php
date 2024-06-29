<?php

namespace Npds\Support\Facades;

use Npds\Auth\Admin as AdminManager;

class Admin
{

    public static function __callStatic($method, $parameters)
    {
        $instance = AdminManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}