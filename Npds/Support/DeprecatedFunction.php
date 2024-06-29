<?php

use Npds\Support\Facades\Language;


/**
 * Formate un timestamp en fonction de la valeur de $locale (config.php)
 * si "nogmt" est concaténé devant la valeur de $time, le décalage gmt n'est pas appliqué
 *
 * @param   [type]  $time  [$time description]
 *
 * @return  [type]         [return description]
 */
function formatTimestamp($time) {
    global $datetime, $locale, $gmt;

    $local_gmt = $gmt;

    setlocale(LC_TIME, Language::aff_langue($locale));

    if (substr($time, 0, 5) == 'nogmt') {
        $time = substr($time, 5);
        $local_gmt = 0;
    }

    preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $time, $datetime);
    $datetime = strftime(translate("datestring"), mktime($datetime[4] + (int)$local_gmt, $datetime[5], $datetime[6], $datetime[2], $datetime[3], $datetime[1]));

    return (ucfirst(htmlentities($datetime, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, cur_charset)));
}
