<?php

namespace Npds\Log;

use Npds\Contracts\Log\LogInterface;


/**
 * Log class
 */
class Log implements LogInterface
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
     * Pour écrire dans un log (security.log par exemple)
     *
     * @param   [type]  $fic_log  [$fic_log description]
     * @param   [type]  $req_log  [$req_log description]
     * @param   [type]  $mot_log  [$mot_log description]
     *
     * @return  [type]            [return description]
     */
    public static function Ecr_Log($fic_log, $req_log, $mot_log)
    {
        // $Fic_log= the file name :
        //  => "security" for security maters
        //  => ""
        // $req_log= a phrase describe the infos
        //
        // $mot_log= if "" the Ip is recorded, else extend status infos
        
        $logfile = "storage/slogs/$fic_log.log";

        $fp = fopen($logfile, 'a');

        flock($fp, 2);
        fseek($fp, filesize($logfile));

        if ($mot_log == "") {
            $mot_log = "IP=>" . getip();
        }

        $ibid = sprintf("%-10s %-60s %-10s\r\n", date("m/d/Y H:i:s", time()), basename($_SERVER['PHP_SELF']) . "=>" . strip_tags(urldecode($req_log)), strip_tags(urldecode($mot_log)));
        
        fwrite($fp, $ibid);
        flock($fp, 3);
        fclose($fp);
    }

}