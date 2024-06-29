<?php

use Npds\Cache\SuperCacheEmpty;
use Npds\Cache\SuperCacheManager;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Metalang;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

if ($SuperCache) {
    $cache_obj = new SuperCacheManager();
} else {
    $cache_obj = new SuperCacheEmpty();
}

include("header.php");

if (($SuperCache) and (!$user)) {
    $cache_obj->startCachingPage();
}

if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($user)) {
    $inclusion = false;

    if (file_exists("themes/$theme/html/top.html")) {
        $inclusion = "themes/$theme/html/top.html";
    } elseif (file_exists("themes/default/html/top.html")) {
        $inclusion = "themes/default/html/top.html";
    } else {
        echo "html/top.html / not find !<br />";
    }

    if ($inclusion) {
        ob_start();
            include($inclusion);
            $Xcontent = ob_get_contents();
        ob_end_clean();

        echo Metalang::meta_lang(Language::aff_langue($Xcontent));
    }
}

// -- SuperCache
if (($SuperCache) and (!$user)) {
    $cache_obj->endCachingPage();
}

include("footer.php");
