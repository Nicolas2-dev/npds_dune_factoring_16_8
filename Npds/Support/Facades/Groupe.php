<?php

namespace Npds\Support\Facades;

use Npds\Groupe\Groupe as GroupeManager;

class Groupe
{

    public static function __callStatic($method, $parameters)
    {
        $instance = GroupeManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}