<?php

namespace Npds\Config;

use Npds\Utility\Arr;
use Npds\Contracts\Config\ConfigInterface;


/**
 * Config class
 */
class Config implements ConfigInterface
{
    /**
     * [$options description]
     *
     * @var [type]
     */
    protected static $options = array();

    
    /**
     * [all description]
     *
     * @return  [type]  [return description]
     */
    public static function all()
    {
        return static::$options;
    }

    /**
     * Return true if the key exists.
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return Arr::array_has(static::$options, $key);
    }

    /**
     * Get the value.
     * 
     * @param string $key
     * @return mixed|null
     */
    public static function get($key, $default = null)
    {
        return Arr::array_get(static::$options, $key, $default);
    }

    /**
     * Set the value.
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        Arr::array_set(static::$options, $key, $value);
    }
    
}