<?php

namespace Npds\Utility;

use Npds\Support\Facades\Mailer;
use Npds\Contracts\Utility\StrInterface;


/**
 * Str class
 */
class Str implements StrInterface
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
     * Encode une chaine UF8 au format javascript
     *
     * @param   [type]  $ibid  [$ibid description]
     *
     * @return  [type]         [return description]
     */    
    public static function utf8_java($ibid)
    {
        // UTF8 = &#x4EB4;&#x6B63;&#7578; 
        // javascript = \u4EB4\u6B63\u.dechex(7578)
        $tmp = explode('&#', $ibid);
        
        foreach ($tmp as $bidon) {
            if ($bidon) {
                $bidon = substr($bidon, 0, strpos($bidon, ";"));
                $hex = strpos($bidon, 'x');

                $ibid = ($hex === false) 
                    ? str_replace('&#' . $bidon . ';', '\\u' . dechex((int)$bidon), $ibid) 
                    : str_replace('&#' . $bidon . ';', '\\u' . substr($bidon, 1), $ibid);
            }
        }

        return $ibid;
    }

    /**
     * Formate une chaine numérique avec un espace tous les 3 chiffres
     *
     * @param   [type]  $ibid  [$ibid description]
     *
     * @return  [type]         [return description]
     */
    public static function wrh($ibid)
    {
        $tmp = number_format($ibid, 0, ',', ' ');
        $tmp = str_replace(' ', '&nbsp;', $tmp);

        return $tmp;
    }
 
    /**
     * convertie \r \n  BR ... en br XHTML
     *
     * @param   [type]  $txt  [$txt description]
     *
     * @return  [type]        [return description]
     */
    public static function conv2br($txt)
    {
        $Xcontent = str_replace("\r\n", "<br />", $txt);
        $Xcontent = str_replace("\r", "<br />", $Xcontent);
        $Xcontent = str_replace("\n", "<br />", $Xcontent);
        $Xcontent = str_replace("<BR />", "<br />", $Xcontent);
        $Xcontent = str_replace("<BR>", "<br />", $Xcontent);

        return $Xcontent;
    }

    /**
     * Les 8 premiers caractères sont convertis en UNE valeur Hexa unique 
     *
     * @param   [type]  $txt  [$txt description]
     *
     * @return  [type]        [return description]
     */
    public static function hexfromchr($txt)
    {
        $surlignage = substr(md5($txt), 0, 8);
        $tmp = 0;
        for ($ix = 0; $ix <= 5; $ix++) {
            $tmp += hexdec($surlignage[$ix]) + 1;
        }

        return ($tmp %= 16);
    }

    /**
     * Découpe la chaine en morceau de $slpit longueur si celle-ci ne contient pas d'espace
     *
     * @param   [type]  $msg    [$msg description]
     * @param   [type]  $split  [$split description]
     *
     * @return  [type]          [return description]
     */    
    public static function split_string_without_space($msg, $split)
    {
        $Xmsg = explode(' ', $msg);
        array_walk($Xmsg, [static::class, 'wrapper_f'], $split);
        $Xmsg = implode(' ', $Xmsg);

        return $Xmsg;
    }

    /**
     * Quote une chaîne contenant des '
     *
     * @param   [type]  $what  [$what description]
     *
     * @return  [type]         [return description]
     */
    public static function FixQuotes($what = '')
    {
        $what = str_replace("&#39;", "'", $what);
        $what = str_replace("'", "''", $what);

        while (preg_match("#\\\\'#", $what)) {
            $what = preg_replace("#\\\\'#", "'", $what);
        }

        return $what;
    }

    /**
     * Fonction Wrapper pour split_string_without_space
     *
     * @param   [type]  $string  [$string description]
     * @param   [type]  $key     [$key description]
     * @param   [type]  $cols    [$cols description]
     *
     * @return  [type]           [return description]
     */
    public static function wrapper_f(&$string, $key, $cols)
    {
        // if (!(stristr($string,'IMG src=') 
        // or stristr($string,'A href=') 
        // or stristr($string,'HTTP:') 
        // or stristr($string,'HTTPS:') 
        // or stristr($string,'MAILTO:') 
        // or stristr($string,'[CODE]'))) 
        // {
            $outlines = '';

            if (strlen($string) > $cols) {
                while (strlen($string) > $cols) {
                    $cur_pos = 0;

                    for ($num = 0; $num < $cols - 1; $num++) {
                        $outlines .= $string[$num];
                        $cur_pos++;

                        if ($string[$num] == "\n") {
                            $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
                            $cur_pos = 0;
                            $num = 0;
                        }
                    }

                    $outlines .= '<i class="fa fa-cut fa-lg"> </i>';
                    $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
                }

                $string = $outlines . $string;
            }
        // }
    }

    /**
     * [changetoamp description]
     *
     * @param   [type]  $r  [$r description]
     *
     * @return  [type]      [return description]
     */
    public static function changetoamp($r)
    {
        return str_replace('&', '&amp;', $r[0]);
    } 

    /**
     * [changetoampadm description]
     *
     * @param   [type]  $r  [$r description]
     *
     * @return  [type]      [return description]
     */
    public static function changetoampadm($r)
    {
        return str_replace('&', '&amp;', $r[0]);
    }
    
    /**
     * [make_clickable description]
     *
     * @param   [type]  $text  [$text description]
     *
     * @return  [type]         [return description]
     */
    public static function make_clickable($text)
    {
        $ret = '';
        $ret = preg_replace('#(^|\s)(http|https|ftp|sftp)(://)([^\s]*)#i', ' <a href="$2$3$4" target="_blank">$2$3$4</a>', $text);
        $ret = preg_replace_callback('#([_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4})#i', [Mailer::class, 'fakedmail'], $ret);
        
        return $ret;
    }
    
    /**
     * [undo_htmlspecialchars description]
     *
     * @param   [type]  $input  [$input description]
     *
     * @return  [type]          [return description]
     */
    public static function undo_htmlspecialchars($input)
    {
        $input = preg_replace("/&gt;/i", ">", $input);
        $input = preg_replace("/&lt;/i", "<", $input);
        $input = preg_replace("/&quot;/i", "\"", $input);
        $input = preg_replace("/&amp;/i", "&", $input);
        
        return $input;
    }

}