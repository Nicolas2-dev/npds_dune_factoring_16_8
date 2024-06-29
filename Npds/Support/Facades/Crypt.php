<?php

namespace Npds\Support\Facades;

use Npds\Encryption\Crypt as CryptManager;

class Crypt
{

    public static function __callStatic($method, $parameters)
    {
        $instance = CryptManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}