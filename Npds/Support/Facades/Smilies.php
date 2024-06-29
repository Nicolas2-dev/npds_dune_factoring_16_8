<?php

namespace Npds\Support\Facades;

use Npds\Pixel\Smilies as SmiliesManager;

class Smilies
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SmiliesManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}