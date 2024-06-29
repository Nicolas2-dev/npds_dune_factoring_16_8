<?php

namespace Npds\Support\Facades;

use Npds\Theme\Theme as ThemeManager;

class Theme
{

    public static function __callStatic($method, $parameters)
    {
        $instance = ThemeManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}