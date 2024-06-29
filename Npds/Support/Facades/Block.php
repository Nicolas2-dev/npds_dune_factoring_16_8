<?php

namespace Npds\Support\Facades;

use Npds\Block\Block as BlockManager;

class Block
{

    public static function __callStatic($method, $parameters)
    {
        $instance = BlockManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}