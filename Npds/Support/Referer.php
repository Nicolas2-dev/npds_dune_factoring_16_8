<?php

namespace Npds\Support;

use Npds\Config\Config;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Request;


/**
 * Referer class
 */
class Referer 
{

    /**
     * [update description]
     *
     * @return  [type]  [return description]
     */
    public static function update()
    {
        if (Config::get('http.referer.httpref') == 1) {
            $referer = htmlentities(strip_tags(Hack::removeHack(getenv("HTTP_REFERER"))), ENT_QUOTES, cur_charset);
        
            if ($referer != '' and !strstr($referer, "unknown") and !stristr($referer, Request::server('SERVER_NAME'))) {
                sql_query("INSERT 
                           INTO " . sql_table('referer') . " 
                           VALUES (NULL, '$referer')");
            }
        }
    }

}