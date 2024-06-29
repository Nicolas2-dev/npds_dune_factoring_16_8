<?php

namespace Npds\Session;

use Npds\Support\Facades\Request;
use Npds\Contracts\Session\SessionInterface;


/**
 * Session class
 */
class Session implements SessionInterface
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
     * Mise à jour la table session
     *
     * @return  [type]  [return description]
     */
    public static function session_manage()
    {
        global $cookie, $REQUEST_URI;

        $guest = 0;
        $ip = Request::getip();
        
        $username = isset($cookie[1]) ? $cookie[1] : $ip; 
        
        if ($username == $ip) {
            $guest = 1;
        }

        // geoloc
        include("modules/geoloc/Config/geoloc.conf");
        
        if ($geo_ip == 1) {
            include "modules/geoloc/Controllers/geoloc_refip.php";
        }

        // geoloc
        $past = time() - 300;
        sql_query("DELETE FROM " . sql_table('session') . " WHERE time < '$past'");

        // proto en test badbotcontrol ...
        // bad robot limited at x connections ...
        // to be defined in config.php ...
        $gulty_robots = array('facebookexternalhit', 'Amazonbot', 'ClaudeBot', 'bingbot', 'Applebot', 'AhrefsBot'); 
        foreach ($gulty_robots as $robot) {
            if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $robot) !== false) {
                $result = sql_query("SELECT agent 
                                     FROM " . sql_table('session') . " 
                                     WHERE agent REGEXP '" . $robot . "'");

                if (sql_num_rows($result) > 3) {
                    header($_SERVER["SERVER_PROTOCOL"] . ' 429 Too Many Requests');
                    echo 'Too Many Requests';
                    die;
                }
            }
        }
        // proto

        $result = sql_query("SELECT time 
                             FROM " . sql_table('session') . " 
                             WHERE username='$username'");

        if ($row = sql_fetch_assoc($result)) {
            if ($row['time'] < (time() - 30)) {
                sql_query("UPDATE " . sql_table('session') . " 
                           SET username='$username', time='" . time() . "', host_addr='$ip', guest='$guest', uri='$REQUEST_URI', agent='" . getenv("HTTP_USER_AGENT") . "' 
                           WHERE username='$username'");
                
                if ($guest == 0) {
                    global $gmt;
                    sql_query("UPDATE " . sql_table('users') . " 
                               SET user_lastvisit='" . (time() + (int)$gmt * 3600) . "' 
                               WHERE uname='$username'");
                }
            }
        } else {
            sql_query("INSERT 
                       INTO " . sql_table('session') . " (username, time, host_addr, guest, uri, agent) 
                       VALUES ('$username', '" . time() . "', '$ip', '$guest', '$REQUEST_URI', '" . getenv("HTTP_USER_AGENT") . "')");
        }
    }

}