<?php

namespace Npds\Cache;

use Npds\Contracts\Cache\SuperCacheEmptyInterface;

/**
 * SuperCacheEmpty class
 */
class SuperCacheEmpty implements SuperCacheEmptyInterface
{
    /**
     * [$genereting_output description]
     *
     * @var [type]
     */
    var $genereting_output;

    /**
     * [$instance description]
     *
     * @var [type]
     */
    protected static $instance;


    /**
     * [getInstance description]
     *
     * @return  [type]  [return description]
     */
    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

}