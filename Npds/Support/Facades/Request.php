<?php

namespace Npds\Support\Facades;

use Npds\Http\Request as RequestManager;

class Request
{

    public static function __callStatic($method, $parameters)
    {
        $instance = RequestManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}