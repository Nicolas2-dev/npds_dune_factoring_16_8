<?php

namespace Npds\Auth;

use Npds\Contracts\Auth\AdminInterface;


/**
 * Admin class
 */
class Admin implements AdminInterface
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
     * Affiche URL et Email d'un auteur
     *
     * @param   [type]  $aid  [$aid description]
     *
     * @return  [type]        [return description]
     */
    public static function formatAidHeader($aid)
    {
        $holder = sql_query("SELECT url, email 
                             FROM " . sql_table('authors') . " WHERE aid='$aid'");

        if ($holder) {
            list($url, $email) = sql_fetch_row($holder);
            
            if (isset($url)) {
                echo '<a href="' . $url . '" >' . $aid . '</a>';
            } elseif (isset($email)) {
                echo '<a href="mailto:' . $email . '" >' . $aid . '</a>';
            } else {
                echo $aid;
            }
        }
    }

}