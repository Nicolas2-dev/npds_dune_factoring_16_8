<?php

use Npds\Support\Facades\Url;
use Npds\Support\Facades\Error;


if (!function_exists("Mysql_Connexion"))
    die();


include('auth.php');

include('modules/geoloc/Controllers/geoloc_locip.php');

filtre_module($file_name);

if (file_exists("modules/comments/$file_name.conf.php"))
    include("modules/comments/$file_name.conf.php");
else
    die();

// settype($forum, 'integer');

if ($forum >= 0)
    die();

// gestion des params du 'forum' : type, accès, modérateur ...
$forum_name = 'comments';
$forum_type = 0;
$allow_to_post = false;

if ($anonpost)
    $forum_access = 0;
else
    $forum_access = 1;

if (($moderate == 1) and $admin)
    $Mmod = true;
elseif ($moderate == 2) {
    $userX = base64_decode($user);
    $userdata = explode(':', $userX);

    $result = sql_query("SELECT level FROM " . sql_table('users_status') . " WHERE uid='" . $userdata[0] . "'");
    list($level) = sql_fetch_row($result);

    if ($level >= 2)
        $Mmod = true;
} else
    $Mmod = false;
// gestion des params du 'forum' : type, accès, modérateur ...

if ($Mmod) {
    switch ($mode) {
        case 'del':
            $sql = "DELETE FROM " . sql_table('posts') . " WHERE forum_id='$forum' AND topic_id = '$topic'";

            if (!$result = sql_query($sql))
                Error::code('0009');

            // ordre de mise à jour d'un champ externe ?
            if ($comments_req_raz != '')
                sql_query("UPDATE " . sql_table($comments_req_raz));

            Url::redirect_url("$url_ret");
            break;

        case 'viewip':
            include("header.php");

            $sql = "SELECT u.uname, p.poster_ip, p.poster_dns FROM " . sql_table('users') . " u, " . sql_table('posts') . " p WHERE p.post_id = '$post' AND u.uid = p.poster_id";
            
            if (!$r = sql_query($sql))
                Error::code('0013');

            if (!$m = sql_fetch_assoc($r))
                Error::code('0014');

            echo '
            <h2 class="mb-3">' . translate("Commentaire") . '</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title mb-3">' . translate("Adresses IP et informations sur les utilisateurs") . '</h3>
                    <div class="row">
                    <div class="col mb-3">
                        <span class="text-body-secondary">' . translate("Identifiant : ") . '</span> ' . $m['uname'] . '<br />
                        <span class="text-body-secondary">' . translate("Adresse IP de l'utilisateur : ") . '</span> ' . $m['poster_ip'] . '<br />
                        <span class="text-body-secondary">' . translate("Adresse DNS de l'utilisateur : ") . '</span> ' . $m['poster_dns'] . '<br />
                    </div>';

            echo localiser_ip($iptoshow = $m['poster_ip']);

            echo '
                </div>
            </div>';

            include('modules/geoloc/Config/geoloc.conf');

            if ($geo_ip == 1)
                echo '
                <div class="card-footer text-end">
                    <a href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&amp;op=allip"><span><i class=" fa fa-globe fa-lg me-1"></i><i class=" fa fa-tv fa-lg me-2"></i></span><span class="d-none d-sm-inline">Carte des IP</span></a>
                </div>';

            echo '
            </div>
            <p><a href="' . rawurldecode($url_ret) . '" class="btn btn-secondary">' . translate("Retour en arrière") . '</a></p>';

            include("footer.php");
            break;

        case 'aff':
            $sql = "UPDATE " . sql_table('posts') . " SET post_aff = '$ordre' WHERE post_id = '$post'";
            sql_query($sql);

            // ordre de mise à jour d'un champ externe ?
            if ($ordre) {
                if ($comments_req_add != '')
                    sql_query("UPDATE " . sql_table($comments_req_add));
            } else {
                if ($comments_req_del != '')
                    sql_query("UPDATE " . sql_table($comments_req_del));
            }

            Url::redirect_url("$url_ret");
            break;
    }
} else {
    include("header.php");

    echo '
        <p class="text-center">' . translate("Vous n'êtes pas identifié comme modérateur de ce forum. Opération interdite.") . '<br /><br />
        <a href="javascript:history.go(-1)" class="btn btn-secondary">' . translate("Retour en arrière") . '</a></p>';

    include("footer.php");
}
