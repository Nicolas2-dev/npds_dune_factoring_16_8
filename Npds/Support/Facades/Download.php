<?php

namespace Npds\Support\Facades;

use Npds\Download\Download as DownloadManager;

class Download
{

    public static function __callStatic($method, $parameters)
    {
        $instance = DownloadManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}