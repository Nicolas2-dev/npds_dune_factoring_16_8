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
     * @var mixed The content of the Response.
     */
    protected $content = '';

    /**
     * @var int HTTP Status
     */
    protected $status = 200;

    /**
     * @var array Array of HTTP headers
     */
    protected $headers = array();

    /**
     * @var array A listing of HTTP status codes
     */
    public static $statuses = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );


    /**
     * [__construct description]
     *
     * @param   [type] $content  [$content description]
     * @param   [type] $status   [$status description]
     * @param   array  $headers  [$headers description]
     * @param   array            [ description]
     *
     * @return  [type]           [return description]
     */
    public function __construct($content = '', $status = 200, array $headers = array())
    {
        if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * [send description]
     *
     * @return  [type]  [return description]
     */
    public function send()
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];

        if (! headers_sent()) {
            $status = $this->status();

            header("$protocol $status " . self::$statuses[$status]);

            foreach ($this->headers as $name => $value) {
                header("$name: $value", true);
            }
        }

        echo $this->render();
    }

    /**
     * [render description]
     *
     * @return  [type]  [return description]
     */
    public function render()
    {
        $content = $this->content();

        if (is_object($content) && method_exists($content, '__toString')) {
            $content = $content->__toString();
        } else {
            $content = (string) $content;
        }

        return trim($content);
    }

    /**
     * [header description]
     *
     * @param   [type]  $name   [$name description]
     * @param   [type]  $value  [$value description]
     *
     * @return  [type]          [return description]
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * [headers description]
     *
     * @return  [type]  [return description]
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * [status description]
     *
     * @param   [type]  $status  [$status description]
     *
     * @return  [type]           [return description]
     */
    public function status($status = null)
    {
        if (is_null($status)) {
            return $this->status;
        } else if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * [content description]
     *
     * @return  [type]  [return description]
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * [__toString description]
     *
     * @return  [type]  [return description]
     */
    public function __toString()
    {
        return $this->render();
    }

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