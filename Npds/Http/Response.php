<?php

namespace Npds\Http;

use Npds\News\Compress\Gzfile;
use Npds\News\Compress\Zipfile;
use Npds\Contracts\Http\ResponseInterface;


/**
 * Response class
 */
class Response implements ResponseInterface
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
     * Controle de réponse c'est pas encore assez fin not work with https probably
     *
     * @param   [type]  $url            [$url description]
     * @param   [type]  $response_code  [$response_code description]
     *
     * @return  [type]                  [return description]
     */
    public static function file_contents_exist($url, $response_code = 200)
    {
        $headers = get_headers($url);
        
        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * compresse et t&eacute;l&eacute;charge un fichier 
     * $line : le flux, $filename et $extension le fichier, $MSos (voir fonction get_os)
     *
     * @param   [type]  $line       [$line description]
     * @param   [type]  $filename   [$filename description]
     * @param   [type]  $extension  [$extension description]
     * @param   [type]  $MSos       [$MSos description]
     *
     * @return  [type]              [return description]
     */
    public static function send_file($line, $filename, $extension, $MSos)
    {
        $compressed = false;

        if (file_exists("lib/archive.php")) {
            if (function_exists("gzcompress")) {
                $compressed = true;
            }
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new Zipfile();
                $filez = $filename . ".zip";
            } else {
                $arc = new Gzfile();
                $filez = $filename . ".gz";
            }

            $arc->addfile($line, $filename . "." . $extension, "");
            $arc->arc_getdata();
            $arc->filedownload($filez);
        } else {
            if ($MSos) {
                header("Content-Type: application/octetstream");
            } else {
                header("Content-Type: application/octet-stream");
            }

            header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $line;
        }
    }

    /**
     * compresse et enregistre un fichier 
     * $line : le flux, $repertoire $filename et $extension le fichier, $MSos (voir fonction get_os)
     *
     * @param   [type]  $line        [$line description]
     * @param   [type]  $repertoire  [$repertoire description]
     * @param   [type]  $filename    [$filename description]
     * @param   [type]  $extension   [$extension description]
     * @param   [type]  $MSos        [$MSos description]
     *
     * @return  [type]               [return description]
     */
    public static function send_tofile($line, $repertoire, $filename, $extension, $MSos)
    {
        $compressed = false;

        if (file_exists("lib/archive.php")) {
            if (function_exists("gzcompress")) {
                $compressed = true;
            }
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new Zipfile();
                $filez = $filename . ".zip";
            } else {
                $arc = new Gzfile();
                $filez = $filename . ".gz";
            }

            $arc->addfile($line, $filename . "." . $extension, "");
            $arc->arc_getdata();

            if (file_exists($repertoire . "/" . $filez)) {
                unlink($repertoire . "/" . $filez);
            }

            $arc->filewrite($repertoire . "/" . $filez, $perms = null);
        } else {
            if ($MSos) {
                header("Content-Type: application/octetstream");
            } else {
                header("Content-Type: application/octet-stream");
            }

            header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $line;
        }
    }

}