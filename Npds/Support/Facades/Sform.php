<?php

namespace Npds\Support\Facades;

use Npds\Sform\SformManager as SformManagerManager;

class Sform
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SformManagerManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}