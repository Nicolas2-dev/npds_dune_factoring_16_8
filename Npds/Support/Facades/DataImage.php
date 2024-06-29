<?php

namespace Npds\Support\Facades;

use Npds\Pixel\DataImage as DataImageManager;

class DataImage
{

    public static function __callStatic($method, $parameters)
    {
        $instance = DataImageManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}