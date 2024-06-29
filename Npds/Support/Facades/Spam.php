<?php

namespace Npds\Support\Facades;

use Npds\Utility\Spam as SpamManager;

class Spam
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SpamManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}