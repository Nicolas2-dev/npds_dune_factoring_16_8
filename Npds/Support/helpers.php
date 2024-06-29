<?php

use Npds\Config\Config;
use Npds\Support\Facades\Log;
use Npds\Support\Facades\Pollbooth;


/**
 * [config description]
 *
 * @param   [type]  $key    [$key description]
 * @param   [type]  $value  [$value description]
 *
 * @return  [type]          [return description]
 */
function config($key, $value = null)
{
    global $$key;

    if (!is_null($value)) {
        $$key = $value;
    }

    return $$key;
}

/**
 * [site_url description]
 *
 * @param   [type]  $url  [$url description]
 *
 * @return  [type]        [return description]
 */
function site_url($url = '')
{
    if (!is_null($url)) {
        $url = ltrim($url, '/');
    }

    return Config::get('npds.nuke_url') . '/' . $url;
}

/**
 * [getip description]
 *
 * @return  [type]  [return description]
 */
// function getip()
// {
//     return Request::getip();
// }

/**
 * [access_denied description]
 *
 * @return  [type]  [return description]
 */
function access_denied()
{
    include("admin/die.php");
}

/**
 * [Access_Error description]
 *
 * @return  [type]  [return description]
 */
function Access_Error()
{
    include("admin/die.php");
}

/**
 * [Admin_alert description]
 *
 * @param   [type]  $motif  [$motif description]
 *
 * @return  [type]          [return description]
 */
function Admin_alert($motif)
{
    global $admin;

    setcookie('admin', '', 0);
    unset($admin);

    Log::Ecr_Log('security', 'auth.inc.php/Admin_alert : ' . $motif, '');

    $Titlesitename = 'NPDS';
    if (file_exists("storage/meta/meta.php")) {
        include("storage/meta/meta.php");
    }

    echo '
        </head>
        <body>
            <br /><br /><br />
            <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;">
                <strong>.: ' . translate("Votre adresse Ip est enregistrée") . ' :.</strong>
            </p>
        </body>
    </html>';
    die();
}

/**
 * retourne true si l'OS de la station cliente est Windows sinon false
 *
 * @return  [type]  [return description]
 */
function get_os()
{
    $client = getenv("HTTP_USER_AGENT");

    if (preg_match('#(\(|; )(Win)#', $client, $regs)) {
        if ($regs[2] == "Win") {
            $MSos = true;
        } else {
            $MSos = false;
        }
    } else {
        $MSos = false;
    }

    return $MSos;
}

// popup

/**
 * Personnalise une ouverture de fenêtre (popup)
 *
 * @param   [type]  $F  [$F description]
 * @param   [type]  $T  [$T description]
 * @param   [type]  $W  [$W description]
 * @param   [type]  $H  [$H description]
 *
 * @return  [type]      [return description]
 */
function JavaPopUp($F, $T, $W, $H)
{
    // 01.feb.2002 by GaWax
    if ($T == "") $T = "@ " . time() . " ";
    $PopUp = "'$F','$T','menubar=no,location=no,directories=no,status=no,copyhistory=no,height=$H,width=$W,toolbar=no,scrollbars=yes,resizable=yes'";
    return $PopUp;
}

// cache 


/**
 * [Q_Select description]
 *
 * @param   [type]  $Xquery     [$Xquery description]
 * @param   [type]  $retention  [$retention description]
 *
 * @return  [type]              [return description]
 */
function Q_Select($Xquery, $retention = 3600)
{
    global $SuperCache, $cache_obj;

    if (($SuperCache) and ($cache_obj)) {
        $row = $cache_obj->CachingQuery($Xquery, $retention);

        return $row;
    } else {
        $result = @sql_query($Xquery);
        $tab_tmp = array();

        while ($row = sql_fetch_assoc($result)) {
            $tab_tmp[] = $row;
        }
        
        return $tab_tmp;
    }
}

/**
 * [PG_clean description]
 *
 * @param   [type]  $request  [$request description]
 *
 * @return  [type]            [return description]
 */
function PG_clean($request)
{
    global $CACHE_CONFIG;

    $page = md5($request);
    $dh = opendir($CACHE_CONFIG['data_dir']);

    while (false !== ($filename = readdir($dh))) {
        if ($filename === '.' 
        or $filename === '..' 
        or (strpos($filename, $page) === FALSE)) {
            continue;
        }

        unlink($CACHE_CONFIG['data_dir'] . $filename);
    }
    closedir($dh);
}

/**
 * [Q_Clean description]
 *
 * @return  [type]  [return description]
 */
function Q_Clean()
{
    global $CACHE_CONFIG;

    $dh = opendir($CACHE_CONFIG['data_dir'] . "sql");

    while (false !== ($filename = readdir($dh))) {
        if ($filename === '.' or $filename === '..') {
            continue;
        }

        if (is_file($CACHE_CONFIG['data_dir'] . "sql/" . $filename)) {
            unlink($CACHE_CONFIG['data_dir'] . "sql/" . $filename);
        }
    }
    closedir($dh);

    $fp = fopen($CACHE_CONFIG['data_dir'] . "sql/.htaccess", 'w');
    @fputs($fp, "Deny from All");
    fclose($fp);
}

/**
 * [SC_clean description]
 *
 * @return  [type]  [return description]
 */
function SC_clean()
{
    global $CACHE_CONFIG;

    $dh = opendir($CACHE_CONFIG['data_dir']);
    while (false !== ($filename = readdir($dh))) {
        if ($filename === '.' 
        or $filename === '..' 
        or $filename === 'ultramode.txt' 
        or $filename === 'net2zone.txt'
        or $filename === 'sql' 
        or $filename === 'index.html') { 
            continue;
        }

        if (is_file($CACHE_CONFIG['data_dir'] . $filename)) {
            unlink($CACHE_CONFIG['data_dir'] . $filename);
        }
    }

    closedir($dh);
    Q_Clean();
}

/**
 * Indique le status de SuperCache
 *
 * @return  [type]  [return description]
 */
function SC_infos()
{
    global $SuperCache, $npds_sc;
    
    $infos = '';
    if ($SuperCache) {
        /*
         $infos = $npds_sc ? '<span class="small">'.translate(".:Page >> Super-Cache:.").'</span>':'';
        */

        if ($npds_sc) {
            $infos = '<span class="small">' . translate(".:Page >> Super-Cache:.") . '</span>';
        } else {
            $infos = '<span class="small">' . translate(".:Page >> Super-Cache:.") . '</span>';
        }
    }

    return $infos;
}

// pollbooth

/**
 * [pollNewest description]
 *
 * @return  [type]  [return description]
 */
function pollNewest() {
    Pollbooth::pollNewest();
}

// debug

/**
 * [_vd description]
 *
 * @return  [type]  [return description]
 */
function _vd() 
{
    array_map(function($value)
    {
       echo '<pre>'.var_dump($value).'</pre>';
    }, func_get_args());
}
 
/**
 * [_dd description]
 *
 * @return  [type]  [return description]
 */
function _dd() 
{
    array_map(function($value)
    {
       echo '<pre>'.var_dump($value).'</pre>';
    }, func_get_args());
    die();
}
