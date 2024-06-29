<?php

use Npds\Config\Config;
use Npds\Support\Facades\News;
use Npds\Support\Facades\User;
use Npds\Cache\SuperCacheEmpty;
use Npds\Support\Facades\Edito;
use Npds\Cache\SuperCacheManager;
use Npds\Support\Facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

/**
 * Redirect for default Start Page of the portal - look at Admin Preferences for choice
 *
 * @return  [type]  [return description]
 */
function select_start_page()
{
    global $index;

    if (!User::AutoReg()) {
        global $user;
        unset($user);
    }

    $op = Request::input('op');

    $start_page = Config::get('npds.start_page');

    if (($start_page == '') 
    or ($op == "index.php") 
    or ($op == "edito") 
    or ($op == "edito-nonews")) 
    {
        $index = 1;

        index();
        die('');
    } else {
        Header("Location: $start_page");
    }
}

/**
 * [index description]
 *
 * @return  [type]  [return description]
 */
function index()
{
    include("header.php");

    // Include cache manager
    $SuperCache = Config::get('cache.super_cache');

    if ($SuperCache) {
        $cache_obj = new SuperCacheManager();
        $cache_obj->startCachingPage();
    } else {
        $cache_obj = new SuperCacheEmpty();
    }

    if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
        // Appel de la publication de News et la purge automatique
        News::automatednews();

        $op         = Request::input('op');
        $catid      = Request::query('catid');
        $marqeur    = Request::query('marqeur');

        global $theme;
        if (($op == 'newcategory') 
        or ($op == 'newtopic') 
        or ($op == 'newindex') 
        or ($op == 'edito-newindex')) 
        {
            //
            News::aff_news($op, $catid, $marqeur);
        } else {
            if (file_exists("Themes/$theme/central.php")) {
                include("Themes/$theme/central.php");
            } else {
                if (($op == 'edito') 
                or ($op == 'edito-nonews')) 
                {
                    //
                    Edito::affEdito();
                }

                if ($op != 'edito-nonews') {
                    //
                    News::aff_news($op, $catid, $marqeur);
                }
            }
        }
    }

    if ($SuperCache) {
        $cache_obj->endCachingPage();
    }

    include("footer.php");
}

switch (Request::input('op')) 
{
    case 'newindex':
    case 'edito-newindex':
    case 'newcategory':
        index();
        break;

    case 'newtopic':
        index();
        break;

    default:
        select_start_page();
        break;
}
