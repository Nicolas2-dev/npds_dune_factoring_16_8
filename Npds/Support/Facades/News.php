<?php

namespace Npds\Support\Facades;

use Npds\News\News as NewsManager;

class News
{

    public static function __callStatic($method, $parameters)
    {
        $instance = NewsManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}