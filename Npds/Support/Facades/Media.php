<?php

namespace Npds\Support\Facades;

use Npds\Media\Video as VideoManager;

class Media
{

    public static function __callStatic($method, $parameters)
    {
        $instance = VideoManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}