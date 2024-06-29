<?php

namespace Npds\Date;

use IntlDateFormatter;
use Npds\Config\Config;
use Npds\Language\Language;
use Npds\Contracts\Date\DateInterface;


/**
 * Date Class
 */
class Date implements DateInterface
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
     * #autodoc formatTimestamp($time) : Formate un timestamp le décalage $gmt défini dans les préférences n'est pas appliqué
     * 
     * [formatTimestamp description]
     *
     * @param   [type]  $time  [$time description]
     *
     * @return  [type]         [return description]
     */
    public static function formatTimestamp($time)
    {
        $fmt = datefmt_create(
            Language::language_iso(1, '_', 1),
            IntlDateFormatter::FULL,
            IntlDateFormatter::MEDIUM,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN,
        );
        
        return ucfirst(
            htmlentities(
                datefmt_format($fmt, strtotime($time)), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, cur_charset)
        );
    }

    /**
     * #autodoc nightDay() : Pour obtenir Nuit ou Jour ... Un grand Merci à P.PECHARD pour cette fonction
     * 
     * [NightDay description]
     *
     * @return  [type]  [return description]
     */
    public static function nightDay()
    {
        $Maintenant = strtotime('now');

        $Jour = strtotime(Config::get('date.lever'));
        $Nuit = strtotime(Config::get('date.coucher'));

        if ($Maintenant - $Jour < 0 xor $Maintenant - $Nuit > 0) {
            return "Nuit";
        } else {
            return "Jour";
        }
    }

    /**
     * [convertDateToTimestamp description]
     *
     * @param   [type]  $myrow  [$myrow description]
     *
     * @return  [type]          [return description]
     */
    public static function convertDateToTimestamp($myrow)
    {
        if (substr($myrow, 2, 1) == "-") {
            $day    = substr($myrow, 0, 2);
            $month  = substr($myrow, 3, 2);
            $year   = substr($myrow, 6, 4);
        } else {
            $day    = substr($myrow, 8, 2);
            $month  = substr($myrow, 5, 2);
            $year   = substr($myrow, 0, 4);
        }

        $hour   = substr($myrow, 11, 2);
        $mns    = substr($myrow, 14, 2);
        $sec    = substr($myrow, 17, 2);
        $tmst   = mktime((int) $hour, (int) $mns, (int) $sec, (int) $month, (int) $day, (int) $year);

        return $tmst;
    }

    /**
     * [postConvertDate description]
     *
     * @param   [type]  $tmst  [$tmst description]
     *
     * @return  [type]         [return description]
     */
    public static function postConvertDate($tmst)
    {
        return $tmst > 0 ? date(translate("dateinternal"), $tmst) : '';
    }
    
    /**
     * [convertDate description]
     *
     * @param   [type]  $myrow  [$myrow description]
     *
     * @return  [type]          [return description]
     */
    public static function convertDate($myrow)
    {
        $tmst = static::convertDateToTimestamp($myrow);
        
        return static::postConvertdate($tmst);
    }

}