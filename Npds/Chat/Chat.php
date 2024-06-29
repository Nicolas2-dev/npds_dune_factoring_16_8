<?php

namespace Npds\Chat;

use Npds\Support\Facades\Str;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\User;
use Npds\Support\Facades\Block;
use Npds\Support\Facades\Request;
use Npds\Contracts\Chat\ChatInterface;


/**
 * Chat class
 */
class Chat implements ChatInterface
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
     * Retourne le nombre de connecté au Chat
     *
     * @param   [type]  $pour  [$pour description]
     *
     * @return  [type]         [return description]
     */
    public static function ifChat($pour)
    {
        $auto = Block::autorisationBlock("params#" . $pour);
        
        $dimauto = count($auto);
        $numofchatters = 0;

        if ($dimauto <= 1) {
            $result = sql_query("SELECT DISTINCT ip 
                                 FROM " . sql_table('chatbox') . " 
                                 WHERE id='" . $auto[0] . "' 
                                 AND date >= " . (time() - (60 * 3)) . "");

            $numofchatters = sql_num_rows($result);
        }

        return $numofchatters;
    }

    /**
     * Insère un record dans la table Chat on utilise id pour filtrer les messages 
     * id = l'id du groupe
     *
     * @param   [type]  $username  [$username description]
     * @param   [type]  $message   [$message description]
     * @param   [type]  $dbname    [$dbname description]
     * @param   [type]  $id        [$id description]
     *
     * @return  [type]             [return description]
     */
    public static function insertChat($username, $message, $dbname, $id)
    {
        if ($message != '') {
            $username = Hack::removeHack(stripslashes(Str::FixQuotes(strip_tags(trim($username)))));
            $message =  Hack::removeHack(stripslashes(Str::FixQuotes(strip_tags(trim($message)))));

            $ip = Request::getip();

            sql_query("INSERT 
                       INTO " . sql_table('chatbox') . " 
                       VALUES ('" . $username . "', '" . $ip . "', '" . $message . "', '" . time() . "', '$id', " . $dbname . ")");
        }
    }

    /**
     * [insertMessageChat description]
     *
     * @return  [type]  [return description]
     */
    public static function insertMessageChat()
    {
        //
        $name       = Request::input('name');
        $message    = Request::input('message');
        $dbname     = Request::input('dbname');
        $id         = Request::input('id');

        //
        $_user      = User::getUser();
        $_cookie    = User::userCookie($_user);

        if (!isset($_cookie[1]) && isset($name)) {
            $uname = $name;
            $dbname = 0;
        } else {
            $uname = $_cookie[1];
            $dbname = 1;
        }

        static::insertChat($uname, $message, $dbname, $id);
    }

    /**
     * [chatBoxWrite description]
     *
     * @return  [type]  [return description]
     */
    public static function chatBoxWrite()
    {
        global $admin;

        if ($admin) {
            $adminX = base64_decode($admin);
            $adminR = explode(':', $adminX);

            $Q = sql_fetch_assoc(sql_query("SELECT * 
                                            FROM " . sql_table('authors') . " 
                                            WHERE aid='$adminR[0]' 
                                            LIMIT 1"));

            if ($Q['radminsuper'] == 1)

                if (Request::query('chatbox_clearDB') == 'OK') {
                    sql_query("DELETE 
                               FROM " . sql_table('chatbox') . " 
                               WHERE date <= " . (time() - (60 * 5)) . "");
                }
        }

        Header("Location: index.php");
    }

}