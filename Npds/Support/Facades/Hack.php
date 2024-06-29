<?php

namespace Npds\Support\Facades;

use Npds\Security\Hack as HackManager;

class Hack
{

    public static function __callStatic($method, $parameters)
    {
        $instance = HackManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}