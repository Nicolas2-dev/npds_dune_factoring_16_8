<?php

namespace Npds\Support\Facades;

use Npds\Http\Response as ResponseManager;

class Response
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ResponseManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}