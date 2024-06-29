<?php

namespace Npds\Support\Facades;

use Npds\Mailer\Mailer as MailerManager;

class Mailer
{

    public static function __callStatic($method, $parameters)
    {
        $instance = MailerManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}