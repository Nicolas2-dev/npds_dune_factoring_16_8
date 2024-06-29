<?php

namespace Npds\Support\Facades;

use Npds\Forum\Forum as ForumManager;

class Forum
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ForumManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}