<?php

namespace Npds\Support\Facades;

use Npds\Messenger\Messenger as MessengerManager;

class Messenger
{

    public static function __callStatic($method, $parameters)
    {
        $instance = MessengerManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}