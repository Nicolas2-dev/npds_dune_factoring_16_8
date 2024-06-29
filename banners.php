<?php

use Npds\Config\Config;
use Npds\Support\Facades\Css;
use Npds\Support\Facades\Str;
use Npds\Support\Facades\Url;
use Npds\Support\Facades\User;
use Npds\Support\Facades\Mailer;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Language;


include("Bootstrap/Boot.php");

/**
 * [viewbanner description]
 *
 * @return  [type]  [return description]
 */
function viewbanner()
{
    $okprint = false;

    $bresult = sql_query("SELECT bid 
                          FROM " . sql_table('banner') . " 
                          WHERE userlevel!='9'");

    $numrows = sql_num_rows($bresult);

    $while_limit = 3;
    $while_cpt = 0;

    while ((!$okprint) and ($while_cpt < $while_limit)) {
        
        // More efficient random stuff, thanks to Cristian Arroyo from http://www.planetalinux.com.ar
        if ($numrows > 0) {
            mt_srand((float) microtime() * 1000000);
            $bannum = mt_rand(0, $numrows);
        } else {
            break;
        }

        $bresult2 = sql_query("SELECT bid, userlevel 
                               FROM " . sql_table('banner') . " 
                               WHERE userlevel!='9' 
                               LIMIT $bannum, 1");

        list($bid, $userlevel) = sql_fetch_row($bresult2);

        if ($userlevel == 0) {
            $okprint = true;
        } else {
            if ($userlevel == 1) {
                if (User::secur_static('member')) { 
                    $okprint = true;
                }
            }

            if ($userlevel == 3) {
                if (User::secur_static('admin')) {
                    $okprint = true;
                }
            }
        }

        $while_cpt = $while_cpt + 1;
    }

    // Le risque est de sortir sans un BID valide
    if (!isset($bid)) {
        $rowQ1 = Q_Select("SELECT bid 
                           FROM " . sql_table('banner') . " 
                           WHERE userlevel='0' 
                           LIMIT 0,1", 86400);

        if ($rowQ1) {

            // erreur à l'install quand on n'a pas de banner dans la base ....
            $myrow      = $rowQ1[0]; 
            $bid        = $myrow['bid'];
            $okprint    = true;
        }
    }

    if ($okprint) {

        if (Config::get('banner.my_ip') != Request::getip()) {
            sql_query("UPDATE " . sql_table('banner') . " 
                       SET impmade=impmade+1 
                       WHERE bid='$bid'");
        }

        if (($numrows > 0) and ($bid)) {
            $aborrar = sql_query("SELECT cid, imptotal, impmade, clicks, imageurl, clickurl, date 
                                  FROM " . sql_table('banner') . " 
                                  WHERE bid='$bid'");

            list($cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = sql_fetch_row($aborrar);

            if ($imptotal == $impmade) {
                sql_query("INSERT 
                           INTO " . sql_table('bannerfinish') . " 
                           VALUES (NULL, '$cid', '$impmade', '$clicks', '$date', now())");

                sql_query("DELETE 
                           FROM " . sql_table('banner') . " 
                           WHERE bid='$bid'");
            }

            if ($imageurl != '') {
                // pourquoi aff_langue sur img src ??
                echo '<a href="'. site_url('banners.php?op=click&amp;bid=' . $bid) . '" target="_blank">
                        <img class="img-fluid" src="' . Language::aff_langue($imageurl) . '" alt="banner" loading="lazy" />
                    </a>';
            } else {
                if (stristr($clickurl, '.txt')) {
                    if (file_exists($clickurl)) {
                        include_once($clickurl);
                    }
                } else {
                    echo $clickurl;
                }
            }
        }
    }
}

/**
 * [clickbanner description]
 *
 * @param   [type]  $bid  [$bid description]
 *
 * @return  [type]        [return description]
 */
function clickbanner()
{
    $bid = Request::query('bid');

    $bresult = sql_query("SELECT clickurl 
                          FROM " . sql_table('banner') . " 
                          WHERE bid='$bid'");

    list($clickurl) = sql_fetch_row($bresult);

    sql_query("UPDATE " . sql_table('banner') . " 
               SET clicks=clicks+1 
               WHERE bid='$bid'");

    sql_free_result($bresult);

    if ($clickurl == '') {
        $clickurl = site_url();
    }

    Header("Location: " . Language::aff_langue($clickurl));
}

/**
 * [clientlogin description]
 *
 * @return  [type]  [return description]
 */
function clientlogin()
{
    header_page();

    echo '
        <div class="card card-body mb-3">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt fa-lg me-3 align-middle"></i>' . translate("Connexion") . '</h3>
            <form id="loginbanner" action="'. site_url('banners.php') .'" method="post">
                <fieldset>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="login" name="login" maxlength="10" required="required" />
                    <label for="login">' . translate("Identifiant ") . '</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="password" id="pass" name="pass" maxlength="10" required="required" />
                    <label for="pass">' . translate("Mot de passe") . '</label>
                    <span class="help-block">' . translate("Merci de saisir vos informations") . '</span>
                </div>
                <input type="hidden" name="op" value="Ok" />
                <button class="btn btn-primary my-3" type="submit">' . translate("Valider") . '</button>
                </div>
                </fieldset>
            </form>
        </div>';

    $arg1 = 'var formulid=["loginbanner"];';

    Css::adminfoot('fv', '', $arg1, 'no');

    footer_page();
}

/**
 * [IncorrectLogin description]
 *
 * @return  [type]  [return description]
 */
function IncorrectLogin()
{
    header_page();

    echo '<div class="alert alert-danger lead">
            ' . translate("Identifiant incorrect !") . '
            <br />
            <button class="btn btn-secondary mt-2" onclick="javascript:history.go(-1)" >
                ' . translate("Retour en arrière") . '
            </button>
        </div>';
    
    footer_page();
}

/**
 * [header_page description]
 *
 * @return  [type]  [return description]
 */
function header_page()
{
    global $Titlesitename, $Default_Theme, $language;

    include_once("modules/upload/Config/upload.conf.php");

    include("storage/meta/meta.php");

    if ($url_upload_css) {
        $url_upload_cssX = str_replace('style.css', $language . '-style.css', $url_upload_css);

        if (is_readable($url_upload . $url_upload_cssX)) {
            $url_upload_css = $url_upload_cssX;
        }

        print('<link href="' . $url_upload . $url_upload_css . '" title="default" rel="stylesheet" type="text/css" media="all" />'."\n");
    }

    if (file_exists('Theme/default/include/header_head.inc')) {
        include('Theme/default/include//header_head.inc');
    }

    if (file_exists('Themes/' . $Default_Theme . '/include/header_head.inc')) {
        include('Themes/' . $Default_Theme . '/include/header_head.inc');
    }

    if (file_exists('Themes/' . $Default_Theme . '/assets/css/style.css')) {
        echo '<link href="Themes/' . $Default_Theme . '/assets/css/style.css" rel="stylesheet" type="text/css" media="all" />';
    }

    echo '
        </head>
        <body style="margin-top:64px;">
            <div class="container-fluid">
            <nav class="navbar navbar-expand-lg fixed-top bg-primary" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="'. site_url('index.php')  .'">
                        <i class="fa fa-home fa-lg me-2"></i>
                    </a>
                    <span class="navbar-text">' . translate("Bannières - Publicité") . '</span>
                </div>
            </nav>
            <h2 class="mt-4">' . translate("Bannières - Publicité") . ' @ ' . $Titlesitename . '</h2>
            <p align="center">';
}

/**
 * [footer_page description]
 *
 * @return  [type]  [return description]
 */
function footer_page()
{
    include('Themes/default/include/footer_after.inc');
    
    echo '</p>
            </div>
        </body>
    </html>';
}

/**
 * [bannerstats description]
 *
 * @param   [type]  $login  [$login description]
 * @param   [type]  $pass   [$pass description]
 *
 * @return  [type]          [return description]
 */
function bannerstats()
{
    $login  = Request::input('login');
    $pass   = Request::input('pass');

    $result = sql_query("SELECT cid, name, passwd 
                         FROM " . sql_table('bannerclient') . " 
                         WHERE login='$login'");

    list($cid, $name, $passwd) = sql_fetch_row($result);

    if ($login == '' and $pass == '' or $pass == '') {
        IncorrectLogin();
    } else {
        if ($pass == $passwd) {
            header_page();

            echo '
            <h3>' . translate("Bannières actives pour") . ' ' . $name . '</h3>
            <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">
                        ' . translate("Id") . '
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">
                        ' . translate("Réalisé") . '
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">
                        ' . translate("Impressions") . '
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">
                        ' . translate("Imp. restantes") . '
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">
                        ' . translate("Clics") . '
                    </th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">
                        % ' . translate("Clics") . '
                    </th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">
                        ' . translate("Fonctions") . '
                    </th>
                </tr>
            </thead>
            <tbody>';

            $result = sql_query("SELECT bid, imptotal, impmade, clicks, date 
                                 FROM " . sql_table('banner') . " 
                                 WHERE cid='$cid'");

            while (list($bid, $imptotal, $impmade, $clicks, $date) = sql_fetch_row($result)) {

                $percent = $impmade == 0 ? '0' : substr(100 * $clicks / $impmade, 0, 5);
                $left = $imptotal == 0 ? translate("Illimité") : $imptotal - $impmade;
                
                echo '
                <tr>
                    <td>
                        ' . $bid . '
                    </td>
                    <td>
                        ' . $impmade . '
                    </td>
                    <td>
                        ' . $imptotal . '
                    </td>
                    <td>
                        ' . $left . '
                    </td>
                    <td>
                        ' . $clicks . '
                    </td>
                    <td>
                        ' . $percent . '%
                    </td>
                    <td>
                        <a href="'. site_url('banners.php?op=EmailStats&amp;login=' . $login . '&amp;cid=' . $cid . '&amp;bid=' . $bid) . '" >
                            <i class="far fa-envelope fa-lg me-2 tooltipbyclass" data-bs-placement="top" title="E-mail Stats"></i>
                        </a>
                    </td>
                </tr>';
            }

            echo '
                </tbody>
            </table>
            <div class="lead my-3">
                <a href="' . site_url() . '" target="_blank">' . Config::get('npds.sitename') . '</a>
            </div>';

            $result = sql_query("SELECT bid, imageurl, clickurl 
                                 FROM " . sql_table('banner') . " 
                                 WHERE cid='$cid'");

            while (list($bid, $imageurl, $clickurl) = sql_fetch_row($result)) {

                // not used !!!
                //$numrows = sql_num_rows($result);

                echo '<div class="card card-body mb-3">';

                if ($imageurl != '') {
                    // pourquoi aff_langue ??
                    //echo '<p><img src="' . Language::aff_langue($imageurl) . '" class="img-fluid" />'; 
                    echo '<p><img src="' . $imageurl . '" class="img-fluid" />'; 
                } else {
                    echo '<p>';
                    echo $clickurl;
                }

                echo '<h4 class="mb-2">Banner ID : ' . $bid . '</h4>';
                
                if ($imageurl != '') {
                    echo '<p>
                            ' . translate("Cette bannière est affichée sur l'url") . ' : 
                            <a href="' . Language::aff_langue($clickurl) . '" target="_Blank" >
                                [ URL ]
                            </a>
                        </p>';
                }

                echo '<form action="'. site_url('banners.php') .'" method="get">';

                if ($imageurl != '') {
                    echo '
                    <div class="mb-3 row">
                        <label class="control-label col-sm-12" for="url">' . translate("Changer") . ' URL</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" name="url" maxlength="200" value="' . $clickurl . '" />
                        </div>
                    </div>';
                } else {
                    echo '
                    <div class="mb-3 row">
                        <label class="control-label col-sm-12" for="url">' . translate("Changer") . ' URL</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" name="url" maxlength="200" value="' . htmlentities($clickurl, ENT_QUOTES, cur_charset) . '" />
                        </div>
                    </div>';
                }

                echo '
                        <input type="hidden" name="login" value="' . $login . '" />
                        <input type="hidden" name="bid" value="' . $bid . '" />
                        <input type="hidden" name="pass" value="' . $pass . '" />
                        <input type="hidden" name="cid" value="' . $cid . '" />
                        <input class="btn btn-primary" type="submit" name="op" value="' . translate("Changer") . '" />
                        </form>
                    </p>
                </div>';
            }

            // Finnished Banners
            echo "<br />";
            
            echo '
            <h3>' . translate("Bannières terminées pour") . ' ' . $name . '</h3>
            <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">
                            ' . translate("Id") . '
                        </td>
                        <th data-halign="center" data-align="right" data-sortable="true">
                            ' . translate("Impressions") . '
                        </th>
                        <th data-halign="center" data-align="right" data-sortable="true">
                            ' . translate("Clics") . '
                        </th>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">
                            % ' . translate("Clics") . '
                        </th>
                        <th data-halign="center" data-align="right" data-sortable="true">
                            ' . translate("Date de début") . '
                        </th>
                        <th data-halign="center" data-align="right" data-sortable="true">
                            ' . translate("Date de fin") . '
                        </th>
                    </tr>
                </thead>
                <tbody>';

            $result = sql_query("SELECT bid, impressions, clicks, datestart, dateend 
                                 FROM " . sql_table('bannerfinish') . " 
                                 WHERE cid='$cid'");

            while (list($bid, $impressions, $clicks, $datestart, $dateend) = sql_fetch_row($result)) {
                $percent = substr(100 * $clicks / $impressions, 0, 5);
                
                echo '
                <tr>
                    <td>
                        ' . $bid . '
                    </td>
                    <td>
                        ' . Str::wrh($impressions) . '
                    </td>
                    <td>
                        ' . $clicks . '
                    </td>
                    <td>
                        ' . $percent . ' %
                    </td>
                    <td>
                        <small>' . $datestart . '</small>
                    </td>
                    <td>
                        <small>' . $dateend . '</small>
                    </td>
                </tr>';
            }

            echo '
                </tbody>
            </table>';

            Css::adminfoot('fv', '', '', 'no');
            footer_page();
        } else {
            IncorrectLogin();
        }
    }
}

/**
 * [EmailStats description]
 *
 * @param   [type]  $login  [$login description]
 * @param   [type]  $cid    [$cid description]
 * @param   [type]  $bid    [$bid description]
 *
 * @return  [type]          [return description]
 */
function EmailStats()
{
    $login  = Request::input('login');
    $cid    = Request::input('cid');
    $bid    = Request::input('bid');

    $result = sql_query("SELECT login 
                         FROM " . sql_table('bannerclient') . " 
                         WHERE cid='$cid'");

    list($loginBD) = sql_fetch_row($result);

    if ($login == $loginBD) {
        $result2 = sql_query("SELECT name, email 
                              FROM " . sql_table('bannerclient') . " 
                              WHERE cid='$cid'");

        list($name, $email) = sql_fetch_row($result2);

        if ($email == '') {
            header_page();
            
            echo '<p align="center">
                    <br />' . translate("Les statistiques pour la bannières ID") . ' : '. $bid . translate("ne peuvent pas être envoyées.") . '
                    <br /><br />
                    ' . translate("Email non rempli pour : ") . ' '. $name .'
                    <br /><br />
                    <a href="javascript:history.go(-1)" >
                        ' . translate("Retour en arrière") . '
                    </a>
                </p>';
            
            footer_page();
        } else {
            $result = sql_query("SELECT bid, imptotal, impmade, clicks, imageurl, clickurl, date 
                                 FROM " . sql_table('banner') . " 
                                 WHERE bid='$bid' 
                                 AND cid='$cid'");

            list($bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = sql_fetch_row($result);
            
            $percent = $impmade == 0 ? '0' : substr(100 * $clicks / $impmade, 0, 5);
            
            if ($imptotal == 0) {
                $left = translate("Illimité");
                $imptotal = translate("Illimité");
            } else {
                $left = $imptotal - $impmade;
            }

            $fecha = date(translate("dateinternal"), time() + ((int) Config::get('date.gmt') * 3600));

            $subject = html_entity_decode(translate("Bannières - Publicité"), ENT_COMPAT | ENT_HTML401, cur_charset) . ' : ' . Config::get('npds.sitename');
            
            $message  = "Client : $name\n" . translate("Bannière") . " ID : $bid\n" . translate("Bannière") . " Image : $imageurl\n" . translate("Bannière") . " URL : $clickurl\n\n";
            $message .= "Impressions " . translate("Réservées") . " : $imptotal\nImpressions " . translate("Réalisées") . " : $impmade\nImpressions " . translate("Restantes") . " : $left\nClicks " . translate("Reçus") . " : $clicks\nClicks " . translate("Pourcentage") . " : $percent%\n\n";
            $message .= translate("Rapport généré le") . ' : ' . "$fecha\n\n";
            
            //
            if (Config::has('signature.signiature')) {
                $message .= Config::get('signature.signiature');
            }

            Mailer::send_email($email, $subject, $message, '', true, 'html', '');
            header_page();

            echo '
            <div class="card bg-light">
                <div class="card-body"
                    <p>
                        ' . $fecha . '
                    </p>
                    <p>
                        ' . translate("Les statistiques pour la bannières ID") . ' : ' . $bid . ' ' . translate("ont été envoyées.") . '
                    </p>
                    <p>
                        ' . $email . ' : Client : ' . $name . '
                    </p>
                    <p>
                        <a href="javascript:history.go(-1)" class="btn btn-primary">
                            ' . translate("Retour en arrière") . '
                        </a>
                    </p>
                </div>
            </div>';
        }
    } else {
        header_page();

        echo '<div class="alert alert-danger">
            ' . translate("Identifiant incorrect !") . '
            <br />' . translate("Merci de") . ' 
            <a href="'. site_url('banners.php?op=login') .'" class="alert-link">
                ' . translate("vous reconnecter.") . '
            </a>
        </div>';
    }

    footer_page();
}

/**
 * [change_banner_url_by_client description]
 *
 * @param   [type]  $pass   [$pass description]
 * @param   [type]  $cid    [$cid description]
 * @param   [type]  $bid    [$bid description]
 * @param   [type]  $url    [$url description]
 *
 * @return  [type]          [return description]
 */
function change_banner_url_by_client()
{
    header_page();

    $pass   = Request::input('pass');
    $cid    = Request::input('cid');
    $bid    = Request::input('bid');
    $url    = Request::input('url');

    $result = sql_query("SELECT passwd 
                         FROM " . sql_table('bannerclient') . " 
                         WHERE cid='$cid'");

    list($passwd) = sql_fetch_row($result);

    if (!empty($pass) and $pass == $passwd) {
        sql_query("UPDATE " . sql_table('banner') . " 
                   SET clickurl='$url' 
                   WHERE bid='$bid'");

        echo '<div class="alert alert-success">
                ' . translate("Vous avez changé l'url de la bannière") . '
                <br />
                <a href="javascript:history.go(-1)" class="alert-link">
                    ' . translate("Retour en arrière") . '
                </a>
            </div>';
    } else {
        echo '<div class="alert alert-danger">
            ' . translate("Identifiant incorrect !") . '
            <br />' . translate("Merci de") . ' 
            <a href="'. site_url('banners.php?op=login') .'" class="alert-link">
                ' . translate("vous reconnecter.") . '
            </a>
        </div>';
    }

    footer_page();
}

switch (Request::input('op')) {
    case 'click':
        clickbanner();
        break;

    case 'login':
        clientlogin();
        break;

    case 'Ok':
        bannerstats();
        break;

    case translate('Changer'):
        change_banner_url_by_client();
        break;

    case 'EmailStats':
        EmailStats();
        break;

    default:
        if (Config::get('banner.banners')) {
            viewbanner();
        } else {
            Url::redirect_url('index.php');
        }
        break;
}
