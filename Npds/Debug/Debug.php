<?php

namespace Npds\Debug;

/**
 * Debug class
 */
class Debug
{
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

    /**
     * [errorReporting description]
     *
     * @param   [type]  $type  [$type description]
     *
     * @return  [type]         [return description]
     */
    public static function Reporting($type)
    {
        // Modify the report level of PHP
        switch($type) {

            // report NO ERROR
            case 'no_error':
                error_reporting(0);
                break;

            // Devel report
            case 'dev_report':
                error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
                break;

            // standard ERROR report
            case 'error_report':
                error_reporting(E_ERROR | E_WARNING | E_PARSE); 
                break;
              
            // all error
            case 'all':
                error_reporting(E_ALL);
                break;    
        }
    }

}