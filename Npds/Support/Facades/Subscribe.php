<?php

namespace Npds\Support\Facades;

use Npds\Subscribe\Subscribe as SubscribeManager;

class Subscribe
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SubscribeManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}