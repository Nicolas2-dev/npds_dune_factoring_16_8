<?php

namespace Npds\Support\Facades;

use Npds\Chat\Chat as ChatManager;

class Chat
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ChatManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}