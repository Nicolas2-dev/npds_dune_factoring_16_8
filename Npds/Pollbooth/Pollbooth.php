<?php

namespace Npds\Pollbooth;

use Npds\Contracts\Pollbooth\PollboothInterface;


/**
 * Pollbooth class
 */
class Pollbooth implements PollboothInterface
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
     * Bloc Sondage 
     * syntaxe : function#pollnewest
     * params#ID_du_sondage OU vide (dernier sondage créé)
     *
     * @param   int   $id  [$id description]
     *
     * @return  void       [return description]
     */
    public static function PollNewest(int $id = null): void
    {
        // snipe : multi-poll evolution
        if ($id != 0) {
            // settype($id, "integer");

            list($ibid, $pollClose) = static::pollSecur($id);

            if ($ibid) {
                pollMain($ibid, $pollClose);
            }

        } elseif ($result = sql_query("SELECT pollID 
                                       FROM " . sql_table('poll_data') . " 
                                       ORDER BY pollID DESC 
                                       LIMIT 1")) 
        {
            list($pollID) = sql_fetch_row($result);
            list($ibid, $pollClose) = static::pollSecur($pollID);
            
            if ($ibid) {
                pollMain($ibid, $pollClose);
            }
        }
    }

    /**
     * Assure la gestion des sondages membres
     *
     * @param   [type]  $pollID  [$pollID description]
     *
     * @return  [type]           [return description]
     */    
    public static function pollSecur($pollID)
    {
        global $user;

        //$pollIDX = false;

        $pollClose = '';
        $result = sql_query("SELECT pollType 
                             FROM " . sql_table('poll_data') . " 
                             WHERE pollID='$pollID'");

        if (sql_num_rows($result)) {
            list($pollType) = sql_fetch_row($result);

            $pollClose = (($pollType / 128) >= 1 ? 1 : 0);
            $pollType = $pollType % 128;

            if (($pollType == 1) and !isset($user)) {
                $pollClose = 99;
            }
        }
        
        return array($pollID, $pollClose);
    }

}