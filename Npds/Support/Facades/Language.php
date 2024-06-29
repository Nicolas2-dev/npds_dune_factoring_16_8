<?php

namespace Npds\Support\Facades;

use Npds\Language\Language as LanguageManager;

class Language
{

    public static function __callStatic($method, $parameters)
    {
        $instance = LanguageManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}