<?php

namespace Npds\Block;

use Npds\Config\Config;
use Npds\Cache\SuperCacheEmpty;
use Npds\Support\Facades\Theme;
use Npds\Support\Facades\Groupe;
use Npds\Cache\SuperCacheManager;
use Npds\Support\Facades\Language;
use Npds\Contracts\Block\BlockInterface;


/**
 * Block class
 */
class Block implements BlockInterface
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
     * Assure la gestion des include# et function# des blocs de NPDS 
     * le titre du bloc est exporté (global) dans $block_title
     *
     * @param   [type]  $title     [$title description]
     * @param   [type]  $contentX  [$contentX description]
     *
     * @return  [type]             [return description]
     */
    public static function blockFonction($title, $contentX)
    {
        global $block_title;
        
        $block_title = $title;
        
        //For including PHP functions in block
        if (stristr($contentX, "function#")) {
            $contentX = str_replace(['<br />', '<BR />', '<BR>'], '', $contentX);

            $contentY = trim(substr($contentX, 9));

            if (stristr($contentY, "params#")) {
                $pos        = strpos($contentY, "params#");
                $contentII  = trim(substr($contentY, 0, $pos));
                $params     = substr($contentY, $pos + 7);
                $prm        = explode(',', $params);

                // Remplace le param "False" par la valeur false (idem pour True)
                for ($i = 0; $i <= count($prm) - 1; $i++) {
                    if ($prm[$i] == "false") {
                        $prm[$i] = false;
                    }

                    if ($prm[$i] == "true") {
                        $prm[$i] = true;
                    }
                }

                // En fonction du nombre de params de la fonction 
                if (function_exists($contentII)) {
                    //
                    call_user_func_array($contentII, $prm);

                    return true;
                } else {
                    return false;
                }
            } else {
                if (function_exists($contentY)) {
                    
                    //
                    call_user_func($contentY);

                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Assure la fabrication réelle et le Cache d'un bloc
     *
     * @param   [type]  $title    [$title description]
     * @param   [type]  $member   [$member description]
     * @param   [type]  $content  [$content description]
     * @param   [type]  $Xcache   [$Xcache description]
     *
     * @return  [type]            [return description]
     */
    public static function fabBlock($title, $member, $content, $Xcache)
    {
        global $CACHE_TIMINGS, $user, $admin;

        // Multi-Langue
        $title = Language::aff_langue($title);

        // Bloc caché
        $hidden = false;
        if (substr($content, 0, 7) == "hidden#") {
            $content = str_replace("hidden#", '', $content);
            $hidden = true;
        }

        // Si on cherche à charger un JS qui a déjà été chargé par pages.php alors on ne le charge pas ...
        global $pages_js;
        if ($pages_js != '') {

            preg_match('#src="([^"]*)#', $content, $jssrc);

            if (is_array($pages_js)) {
                foreach ($pages_js as $jsvalue) {
                    if (array_key_exists('1', $jssrc)) {
                        if ($jsvalue == $jssrc[1]) {
                            $content = '';
                            break;
                        }
                    }
                }
            } else {
                if (array_key_exists('1', $jssrc)) {
                    if ($pages_js == $jssrc[1]) {
                        $content = "";
                    }
                }
            }
        }

        $content = Language::aff_langue($content);

        $super_cache = Config::get('cache.super_cache');

        if (($super_cache) and ($Xcache != 0)) {
            $cache_clef = md5($content);
            $CACHE_TIMINGS[$cache_clef] = $Xcache;
            $cache_obj = new SuperCacheManager();
            $cache_obj->startCachingBlock($cache_clef);
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$super_cache) or ($Xcache == 0)) {

            // For including CLASS AND URI in Block
            global $B_class_title, $B_class_content;

            $B_class_title = '';
            $B_class_content = '';
            $R_uri = '';

            if (stristr($content, 'class-') or stristr($content, 'uri')) {
                $tmp = explode("\n", $content);
                $content = '';

                foreach ($tmp as $id => $class) {
                    $temp = explode("#", $class);

                    if ($temp[0] == "class-title") {
                        $B_class_title = str_replace("\r", "", $temp[1]);
                    } else if ($temp[0] == "class-content") {
                        $B_class_content = str_replace("\r", "", $temp[1]);
                    } else if ($temp[0] == "uri") {
                        $R_uri = str_replace("\r", '', $temp[1]);
                    } else {
                        if ($content != '') {
                            $content .= "\n ";
                        }

                        $content .= str_replace("\r", '', $class);
                    }
                }
            }

            // For BLOC URIs
            if ($R_uri) {
                global $REQUEST_URI;

                $page_ref = basename($REQUEST_URI);
                $tab_uri = explode(" ", $R_uri);

                $R_content = false;

                $tab_pref = parse_url($page_ref);
                $racine_page = $tab_pref['path'];

                if (array_key_exists('query', $tab_pref)) {
                    $tab_pref = explode('&', $tab_pref['query']);
                }

                foreach ($tab_uri as $RR_uri) {
                    $tab_puri = parse_url($RR_uri);
                    $racine_uri = $tab_puri['path'];

                    if ($racine_page == $racine_uri) {
                        if (array_key_exists('query', $tab_puri)) {
                            $tab_puri = explode('&', $tab_puri['query']);
                        }

                        foreach ($tab_puri as $idx => $RRR_uri) {
                            if (substr($RRR_uri, -1) == "*") {
                                // si le token contient *
                                if (substr($RRR_uri, 0, strpos($RRR_uri, "=")) == substr($tab_pref[$idx], 0, strpos($tab_pref[$idx], "="))) {
                                    $R_content = true;
                                }
                            } else {
                                if ($RRR_uri != $tab_pref[$idx]) {
                                    $R_content = false;
                                } else {
                                    $R_content = true;
                                }
                            }
                        }
                    }

                    if ($R_content == true) {
                        break;
                    }
                }

                if (!$R_content) {
                    $content = '';
                }
            }

            // For Javascript in Block
            if (!stristr($content, 'javascript')) {
                $content = nl2br($content);
            }

            // For including externale file in block / the return MUST BE in $content
            if (stristr($content, 'include#')) {
                $Xcontent = false;

                // You can now, include AND cast a fonction with params in the same bloc !
                if (stristr($content, "function#")) {
                    $content = str_replace('<br />', '', $content);
                    $content = str_replace('<BR />', '', $content);
                    $content = str_replace('<BR>', '', $content);

                    $pos = strpos($content, 'function#');

                    $Xcontent = substr(trim($content), $pos);
                    $content = substr(trim($content), 8, $pos - 10);
                } else {
                    $content = substr(trim($content), 8);
                }

                include_once($content);

                if ($Xcontent) {
                    $content = $Xcontent;
                }
            }

            if (!empty($content)) {
                if (($member == 1) and (isset($user))) {
                    static::renderBlock($hidden, $title, $content);
                } elseif ($member == 0) {
                    static::renderBlock($hidden, $title, $content);
                } elseif (($member > 1) and (isset($user))) {
                    $tab_groupe = Groupe::valid_group($user);

                    if (Groupe::groupe_autorisation($member, $tab_groupe)) {
                        static::renderBlock($hidden, $title, $content);
                    }
                } elseif (($member == -1) and (!isset($user))) {
                    static::renderBlock($hidden, $title, $content);
                } elseif (($member == -127) and (isset($admin)) and ($admin)) {
                    static::renderBlock($hidden, $title, $content);
                }
            }

            if (($super_cache) and ($Xcache != 0)) {
                $cache_obj->endCachingBlock($cache_clef);
            }
        }
    }

    /**
     * [renderBlock description]
     *
     * @param   [type]  $hidden   [$hidden description]
     * @param   [type]  $title    [$title description]
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public static function renderBlock($hidden, $title, $content)
    {
        if (!static::blockFonction($title, $content)) {
            if (!$hidden) {
                Theme::themesidebox($title, $content);
            } else {
                echo $content;
            }
        }
    }

    /**
     * Blocs de Gauche
     *
     * @param   [type]  $moreclass  [$moreclass description]
     *
     * @return  [type]              [return description]
     */
    public static function leftBlocks($moreclass)
    {
        static::PreFabBlock('', 'LB', $moreclass);
    }

    /**
     * Blocs de Droite
     *
     * @param   [type]  $moreclass  [$moreclass description]
     *
     * @return  [type]              [return description]
     */
    public static function rightBlocks($moreclass)
    {
        static::PreFabBlock('', 'RB', $moreclass);
    }

    /**
     * 
     *
     * @param   [type]  $Xid     [$Xid description]
     * @param   [type]  $Xblock  [$Xblock description]
     *
     * @return  [type]           [return description]
     */    
    public static function oneblock($Xid, $Xblock)
    {
        ob_start();
            static::PreFabBlock($Xid, $Xblock, '');
            $tmp = ob_get_contents();
        ob_end_clean();

        return $tmp;
    }
 
    /**
     * Assure la fabrication d'un ou de tous les blocs Gauche et Droite
     *
     * @param   [type]  $Xid        [$Xid description]
     * @param   [type]  $Xblock     [$Xblock description]
     * @param   [type]  $moreclass  [$moreclass description]
     *
     * @return  [type]              [return description]
     */
    public static function PreFabBlock($Xid, $Xblock, $moreclass)
    {
        global $htvar, $bloc_side;

        if ($Xid)
            $result = $Xblock == 'RB' 
                ? sql_query("SELECT title, content, member, cache, actif, id, css 
                             FROM " . sql_table('rblocks') . " 
                             WHERE id='$Xid'") 

                : sql_query("SELECT title, content, member, cache, actif, id, css 
                             FROM " . sql_table('lblocks') . " 
                             WHERE id='$Xid'");
        else
            $result = $Xblock == 'RB' 
                ? sql_query("SELECT title, content, member, cache, actif, id, css 
                             FROM " . sql_table('rblocks') . " 
                             ORDER BY Rindex ASC") 

                : sql_query("SELECT title, content, member, cache, actif, id, css 
                             FROM " . sql_table('lblocks') . " 
                             ORDER BY Lindex ASC");

        $bloc_side = $Xblock == 'RB' ? 'RIGHT' : 'LEFT';

        while (list($title, $content, $member, $cache, $actif, $id, $css) = sql_fetch_row($result)) {
            if (($actif) or ($Xid)) {
                if ($css == 1) {
                    $htvar = '<div class="' . $moreclass . '" id="' . $Xblock . '_' . $id . '">'; 
                } else {
                    $htvar = '<div class="' . $moreclass . ' ' . strtolower($bloc_side) . 'bloc">'; 
                }

                static::fabBlock($title, $member, $content, $cache);
                // echo "</div>"; 
            }
        }

        sql_free_result($result);
    }

    /**
     * Retourne le niveau d'autorisation d'un block (et donc de certaines fonctions)
     * le paramètre (une expression régulière) est le contenu du bloc (function#....)
     *
     * @param   [type]  $Xcontent  [$Xcontent description]
     *
     * @return  [type]             [return description]
     */
    public static function nivBlock($Xcontent)
    {
        $result = sql_query("SELECT member, actif 
                             FROM " . sql_table('rblocks') . " 
                             WHERE content REGEXP '$Xcontent'");

        if (sql_num_rows($result)) {
            list($member, $actif) = sql_fetch_row($result);

            return ($member . ',' . $actif);
        }

        $result = sql_query("SELECT member, actif 
                             FROM " . sql_table('lblocks') . " 
                             WHERE content REGEXP '$Xcontent'");

        if (sql_num_rows($result)) {
            list($member, $actif) = sql_fetch_row($result);

            return ($member . ',' . $actif);
        }

        sql_free_result($result);
    }
    
    /**
     * Retourne une chaine ??
     * array ou vide contenant la liste des autorisations (-127,-1,0,1,2...126)) SI le bloc est actif SINON ""
     * le paramètre est le contenu du bloc (function#....)
     * 
     * @param   [type]  $Xcontent  [$Xcontent description]
     *
     * @return  [type]             [return description]
     */
    public static function autorisationBlock($Xcontent)
    {
        $autoX = array(); 

        $auto = explode(',', static::nivBlock($Xcontent));

        // le dernier indice indique si le bloc est actif
        $actif = $auto[count($auto) - 1];

        // on dépile le dernier indice
        array_pop($auto);

        foreach ($auto as $autovalue) {
            if (Groupe::autorisation($autovalue))
                $autoX[] = $autovalue;
        }

        if ($actif) {
            return $autoX;
        } else {
            return '';
        }
    }
    
}