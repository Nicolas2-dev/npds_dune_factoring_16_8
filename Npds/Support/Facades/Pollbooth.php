<?php

namespace Npds\Support\Facades;

use Npds\Pollbooth\Pollbooth as PollboothManager;

class Pollbooth
{

    public static function __callStatic($method, $parameters)
    {
        $instance = PollboothManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}