<?php

namespace Npds\Support\Facades;

use Npds\Asset\Js as JsManager;

class Js
{

    public static function __callStatic($method, $parameters)
    {
        $instance = JsManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}