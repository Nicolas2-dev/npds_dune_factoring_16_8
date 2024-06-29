<?php

namespace Npds\Support\Facades;

use Npds\Editeur\Editeur as EditeurManager;

class Editeur
{

    public static function __callStatic($method, $parameters)
    {
        $instance = EditeurManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}