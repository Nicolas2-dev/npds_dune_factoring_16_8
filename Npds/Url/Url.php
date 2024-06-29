<?php

namespace Npds\Url;

use Npds\Contracts\Url\UrlInterface;


/**
 * Url class
 */
class Url implements UrlInterface
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
     * Permet une redirection javascript / en lieu et place de header("location: ...");
     *
     * @param   [type]  $urlx  [$urlx description]
     *
     * @return  [type]         [return description]
     */
    public static function redirect_url($urlx)
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='" . site_url($urlx) . "';\n";
        echo "//]]>\n";
        echo "</script>";
    }

}