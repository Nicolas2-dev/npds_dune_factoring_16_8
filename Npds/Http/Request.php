<?php


namespace Npds\Http;

use Npds\Utility\Arr;
use Npds\Http\HttpProtect;
use Npds\Contracts\Http\RequestInterface;


/**
 * Request class
 */
class Request implements RequestInterface
{
    /**
     * [$instance description]
     *
     * @var [type]
     */
    protected static $instance;

    /**
     * [$method description]
     *
     * @var [type]
     */
    protected $method;

    /**
     * [$headers description]
     *
     * @var [type]
     */
    protected $headers;

    /**
     * [$server description]
     *
     * @var [type]
     */
    protected $server;

    /**
     * [$query description]
     *
     * @var [type]
     */
    protected $query;

    /**
     * [$post description]
     *
     * @var [type]
     */
    protected $post;

    /**
     * [$files description]
     *
     * @var [type]
     */
    protected $files;

    /**
     * [$cookies description]
     *
     * @var [type]
     */
    protected $cookies;


    /**
     * [__construct description]
     *
     * @param   [type] $method   [$method description]
     * @param   array  $headers  [$headers description]
     * @param   array  $server   [$server description]
     * @param   array  $query    [$query description]
     * @param   array  $post     [$post description]
     * @param   array  $files    [$files description]
     * @param   array  $cookies  [$cookies description]
     *
     * @return  [type]           [return description]
     */
    public function __construct($method, array $headers, array $server, array $query, array $post, array $files, array $cookies)
    {
        //
        $this->method = strtoupper($method);

        //
        $this->headers = array_change_key_case($headers);

        //
        $this->server  = $server;

        //
        $this->query   = $query;

        //
        $this->post    = $post;

        //
        $this->files   = $files;

        //
        $this->cookies = $cookies;
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

        // Get the HTTP method.
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        // Get the request headers.
        $headers = apache_request_headers();

        array_walk_recursive($_GET, [HttpProtect::class, 'addslashes_GPC']);
        array_walk_recursive($_GET, [HttpProtect::class, 'url_protect']);
        array_walk_recursive($_POST, [HttpProtect::class, 'addslashes_GPC']);

        return static::$instance = new static($method, $headers, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }

    /**
     * [getip description]
     *
     * @return  [type]  [return description]
     */
    public function getip()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        if (strpos($realip, ",") > 0) {
            $realip = substr($realip, 0, strpos($realip, ",") - 1);
        }

        return urlencode(trim($realip));
    }

    /**
     * [ajax description]
     *
     * @return  [type]  [return description]
     */
    public function ajax()
    {
        if (! is_null($header = Arr::array_get($this->server, 'HTTP_X_REQUESTED_WITH'))) {
            return strtolower($header) === 'xmlhttprequest';
        }

        return false;
    }

    /**
     * [previous description]
     *
     * @return  [type]  [return description]
     */
    public function previous()
    {
        return Arr::array_get($this->server, 'HTTP_REFERER');
    }

    /**
     * [server description]
     *
     * @param   [type]  $key  [$key description]
     *
     * @return  [type]        [return description]
     */
    public function server($key = null)
    {
        if (is_null($key)) {
            return $this->server;
        }

        return Arr::array_get($this->server, $key);
    }

    /**
     * [input description]
     *
     * @param   [type]  $key      [$key description]
     * @param   [type]  $default  [$default description]
     *
     * @return  [type]            [return description]
     */
    public function input($key, $default = null)
    {
        $input = ($this->method == 'GET') ? $this->query : $this->post;

        return Arr::array_get($input, $key, $default);
    }

    /**
     * [inputAll description]
     *
     * @return  [type]  [return description]
     */
    public function inputAll()
    {
        return $this->post;
    }

    /**
     * [query description]
     *
     * @param   [type]  $key      [$key description]
     * @param   [type]  $default  [$default description]
     *
     * @return  [type]            [return description]
     */
    public function query($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->query;
        }

        return Arr::array_get($this->query, $key, $default);
    }

    /**
     * [queryAll description]
     *
     * @return  [type]  [return description]
     */
    public function queryAll()
    {
        return $this->query;
    }

    /**
     * [files description]
     *
     * @return  [type]  [return description]
     */
    public function files()
    {
        return $this->files;
    }

    /**
     * [file description]
     *
     * @param   [type]  $key  [$key description]
     *
     * @return  [type]        [return description]
     */
    public function file($key)
    {
        return Arr::array_get($this->files, $key);
    }

    /**
     * [hasFile description]
     *
     * @param   [type]  $key  [$key description]
     *
     * @return  [type]        [return description]
     */
    public function hasFile($key)
    {
        return Arr::array_has($this->files, $key);
    }

    /**
     * [cookies description]
     *
     * @return  [type]  [return description]
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * [cookie description]
     *
     * @param   [type]  $key      [$key description]
     * @param   [type]  $default  [$default description]
     *
     * @return  [type]            [return description]
     */
    public function cookie($key, $default = null)
    {
        return Arr::array_get($this->cookies, $key, $default);
    }

    /**
     * [hasCookie description]
     *
     * @param   [type]  $key  [$key description]
     *
     * @return  [type]        [return description]
     */
    public function hasCookie($key)
    {
        return Arr::array_has($this->cookies, $key);
    }
    
}