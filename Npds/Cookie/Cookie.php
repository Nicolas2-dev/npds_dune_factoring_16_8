<?php

namespace Npds\cookie;

use Npds\Contracts\Cookie\CookieInterface;


/**
 * Cookie class
 */
class Cookie implements CookieInterface
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
     * Décode le cookie membre et vérifie certaines choses (password)
     *
     * @param   [type]  $user  [$user description]
     *
     * @return  [type]         [return description]
     */
    public static function decode($user)
    {
        global $language;
        
        $stop = false;

        if (array_key_exists("user", $_GET)) {
            if ($_GET['user'] != '') {
                $stop = true;
                $user = "BAD-GET";
            }
        } else if (isset($HTTP_GET_VARS)) {
            if (array_key_exists("user", $HTTP_GET_VARS)) {
                $stop = true;
                $user = "BAD-GET";
            }
        }

        if ($user) {
            $cookie = explode(':', base64_decode($user));

            // settype($cookie[0], "integer");

            if (trim($cookie[1]) != '') {
                $result = sql_query("SELECT pass, user_langue 
                                     FROM " . sql_table('users') . " 
                                     WHERE uname='$cookie[1]'");

                if (sql_num_rows($result) == 1) {

                    list($pass, $user_langue) = sql_fetch_row($result);

                    if (($cookie[2] == md5($pass)) and ($pass != '')) {
                        if ($language != $user_langue) {
                            sql_query("UPDATE " . sql_table('users') . " 
                                       SET user_langue='$language' 
                                       WHERE uname='$cookie[1]'");
                        }

                        return $cookie;
                    } else {
                        $stop = true;
                    }
                } else {
                    $stop = true;
                }
            } else {
                $stop = true;
            }

            if ($stop) {
                setcookie('user', '', 0);
                unset($user);
                unset($cookie);
                header("Location: index.php");
            }
        }
    }

}