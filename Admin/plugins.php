<?php

use Npds\Support\Facades\Url;


if (!function_exists('admindroits'))
    include('die.php');

include("header.php");

if ($ModPath != '') {
    if (file_exists("modules/$ModPath/$ModStart.php"))
        include("modules/$ModPath/$ModStart.php");
} else
    Url::redirect_url(urldecode($ModStart));
