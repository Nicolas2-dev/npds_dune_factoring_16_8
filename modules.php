<?php


include("Bootstrap/Boot.php");


function filtre_module($strtmp)
{
    if (strstr($strtmp, '..') 
    || stristr($strtmp, 'script') 
    || stristr($strtmp, 'cookie') 
    || stristr($strtmp, 'iframe') 
    || stristr($strtmp, 'applet') 
    || stristr($strtmp, 'object')) {
        Access_Error();
    } else {
        return $strtmp != '' ? true : false;
    }
}

if (filtre_module($ModPath) and filtre_module($ModStart)) {

    if (file_exists("modules/$ModPath/Controllers/$ModStart.php")) {
        include("modules/$ModPath/Controllers/$ModStart.php");
        die();
    } elseif (file_exists("modules/$ModPath/$ModStart.php")) {
        include("modules/$ModPath/$ModStart.php");
        die();
    } else {
        Access_Error();
    }
} else {
    Access_Error();
}
