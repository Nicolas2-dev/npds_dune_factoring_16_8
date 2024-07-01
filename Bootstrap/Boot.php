<?php

use Npds\Auth\User;
use Npds\Support\Npds;
use Npds\Boot\NpdsBoot;
use Npds\Config\Config;
use Npds\Session\Session;
use Npds\Boot\AliasLoader;
use Npds\Http\HttpProtect;
use Npds\Metalang\Metalang;
use Npds\Support\Facades\Debug;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Language;
use Npds\Execption\ExecptionHandler;

// fichier non terminée et sera deprecieé bientot !

//
require 'vendor/autoload.php';

if (!defined('NPDS_GRAB_GLOBALS_INCLUDED')) {
    define('NPDS_GRAB_GLOBALS_INCLUDED', 1);

    if (!defined("LOCK_EX")) {
        define("LOCK_EX", 2);
    }

    if (!defined("LOCK_EX")) {
        define("LOCK_UN", 3);
    }

    include("Config/Deprecated/Config.php");

    // Load the configuration files.
    foreach (glob('Config/*.php') as $path) {
        $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));

        Config::set($key, require($path));
    }

    // Initialize the Aliases Loader.
    AliasLoader::initialize();

    // changer la valeur a true pour activé le debugeur false pour désactivé.
    if (Config::get('debug')) {

        // Modify the report level of PHP
        Debug::reporting('all');
        
        // error_reporting(-1);

        // ini_set('display_errors', 'Off');

        // // Initialize the Exceptions Handler.
        // ExecptionHandler::initialize();
    }

    new NpdsBoot();

    // Get values, slash, filter and extract
    if (!empty($_GET)) {
        array_walk_recursive($_GET, [HttpProtect::class, 'addslashes_GPC']);
        reset($_GET);
        array_walk_recursive($_GET, [HttpProtect::class, 'url_protect']);
        extract(Request::queryAll(), EXTR_OVERWRITE);
    }

    if (!empty($_POST)) {
        array_walk_recursive($_POST, [HttpProtect::class, 'addslashes_GPC']);

        extract(Request::inputAll(), EXTR_OVERWRITE);
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_COOKIE)) {
        extract($_COOKIE, EXTR_OVERWRITE);
    }

    //$user = $request->cookie('user');

    if (isset($user)) {
        $ibid = explode(':', base64_decode($user));
        array_walk($ibid, [HttpProtect::class, 'url_protect']);
        $user = base64_encode(str_replace("%3A", ":", urlencode(base64_decode($user))));
    }

    //$user_language = $request->cookie('user_language');

    if (isset($user_language)) {
        $ibid = explode(':', $user_language);
        array_walk($ibid, [HttpProtect::class, 'url_protect']);
        $user_language = str_replace("%3A", ":", urlencode($user_language));
    }

    //$admin = $request->cookie('admin');

    if (isset($admin)) {
        $ibid = explode(':', base64_decode($admin));
        array_walk($ibid, [HttpProtect::class, 'url_protect']);
        $admin = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($admin))));
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_SERVER)) {
        extract($_SERVER, EXTR_OVERWRITE);
    }

    if (!empty($_ENV)) {
        extract($_ENV, EXTR_OVERWRITE);
    }

    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $value) {
            $$key = $value['tmp_name'];
        }
    }

    //include database 
    include("Npds/Database/Database.php");

    //
    Mysql_Connexion();

    //
    Npds::npds_php_version();

    // 
    require_once("auth.inc.php");

    global $user, $cookie;
    $cookie = User::userCookie($user);

    //
    Session::session_manage();

    //
    $tab_langue = Language::make_tab_langue();

    //
    Metalang::charg_metalang();


    date_default_timezone_set(Config::get('npds.timezone')); 
}
