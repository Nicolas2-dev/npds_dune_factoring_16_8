<?php


namespace Npds\Http;

use Npds\Config\Config;
use Npds\Contracts\Http\HttpProtectInterface;


/**
 * HttpProtect class
 */
class HttpProtect implements HttpProtectInterface
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
     * [addslashes_GPC description]
     *
     * @param   [type]  $arr  [$arr description]
     *
     * @return  [type]        [return description]
     */
    public static function addslashes_GPC(&$arr)
    {
        $arr = addslashes($arr);
    }
    
    /**
     * [url_protect description]
     *
     * @param   [type]  $arr  [$arr description]
     * @param   [type]  $key  [$key description]
     *
     * @return  [type]        [return description]
     */
    public static function url_protect($arr, $key)
    {
        $arr = rawurldecode($arr);
        $RQ_tmp = strtolower($arr);
        $RQ_tmp_large = strtolower($key) . "=" . $RQ_tmp;

        $bad_uri_content = Config::get('url_protect.bad_uri_content');

        if (
            in_array($RQ_tmp, $bad_uri_content)
            or
            in_array($RQ_tmp_large, $bad_uri_content)
            or
            in_array($key, static::bad_uri_key(), true)
            or
            count(static::badname_in_uri()) > 0
        ) {
            access_denied();
        }
    }

    /**
     * [bad_uri_key description]
     *
     * @return  [type]  [return description]
     */
    private static function bad_uri_key()
    {
        return array_keys($_SERVER);
    }
    
    /**
     * [badname_in_uri description]
     *
     * @return  [type]  [return description]
     */
    private static function badname_in_uri() 
    {
        return array_intersect(array_keys($_GET), Config::get('url_protect.bad_uri_name'));
    }

}