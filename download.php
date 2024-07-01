<?php

use Npds\Config\Config;
use Npds\Routing\Controller;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Groupe;
use Npds\Support\Facades\Mailer;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Download;

if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

class DownloadController extends Controller
{

    /**
     * [main description]
     *
     * @return  [type]  [return description]
     */
    protected function main()
    {
        global $dcategory, $sortby, $sortorder;

        $dcategory  = Hack::removeHack(stripslashes(htmlspecialchars(urldecode($dcategory ? $dcategory : ''), ENT_QUOTES, cur_charset))); 
        $dcategory  = str_replace("&#039;", "\'", $dcategory);
        $sortby     = Hack::removeHack(stripslashes(htmlspecialchars(urldecode($sortby ? $sortby : ''), ENT_QUOTES, cur_charset)));

        include("header.php");

        echo '
        <h2>' . translate("Chargement de fichiers") . '</h2>
        <hr />';

        Download::tlist();

        if ($dcategory != translate("Aucune catégorie")) {
            Download::listdownloads($dcategory, $sortby, $sortorder);
        }

        if (file_exists("storage/static/download.ban.txt")) {
            include("storage/static/download.ban.txt");
        }

        include("footer.php");
    }

    /**
     * [transferfile description]
     *
     * @param   [type]  $did  [$did description]
     *
     * @return  [type]        [return description]
     */
    protected function transferfile()
    {
        $did = Request::query('did');

        $result = sql_query("SELECT dcounter, durl, perms 
                            FROM " . sql_table('downloads') . " 
                            WHERE did='$did'");

        list($dcounter, $durl, $dperm) = sql_fetch_row($result);

        if (!$durl) {
            include("header.php");

            echo '
            <h2>' . translate("Chargement de fichiers") . '</h2>
            <hr />
            <div class="lead alert alert-danger">' . translate("Ce fichier n'existe pas ...") . '</div>';

            include("footer.php");
        } else {
            if (stristr($dperm, ',')) {
                $ibid = explode(',', $dperm);

                foreach ($ibid as $v) {
                    $aut = true;

                    if (Groupe::autorisation($v) == true) {
                        $dcounter++;
                        sql_query("UPDATE " . sql_table('downloads') . " 
                                SET dcounter='$dcounter' 
                                WHERE did='$did'");

                        header("location: " . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
                        break;
                    } else {
                        $aut = false;
                    }
                }

                if ($aut == false) {
                    Header('Location: '. site_url('download.php'));
                }
            } else {
                if (Groupe::autorisation($dperm)) {
                    $dcounter++;
                    sql_query("UPDATE " . sql_table('downloads') . " 
                            SET dcounter='$dcounter' 
                            WHERE did='$did'");

                    header("location: " . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
                } else {
                    Header('Location: '. site_url('download.php'));
                }
            }
        }
    }

    /**
     * [broken description]
     *
     * @param   [type]  $did  [$did description]
     *
     * @return  [type]        [return description]
     */
    protected function broken()
    {
        global $user, $cookie;

        if ($user) {

            $did = Request::query('did');

            if ($did) {

                $message = site_url() . "\n" . translate("Téléchargements") . " ID : $did\n" . translate("Auteur") . " $cookie[1] / IP : " . Request::getip() . "\n\n";
                
                //
                if (Config::has('signature.signiature')) {
                    $message .= Config::get('signature.signiature');
                }

                Mailer::send_email(Config::get('mailer.notify_email'), html_entity_decode(translate("Rapporter un lien rompu"), ENT_COMPAT | ENT_HTML401, cur_charset), nl2br($message), Config::get('mailer.notify_from'), false, "html", '');
                
                include("header.php");

                echo '
                <div class="alert alert-success">
                <p class="lead">' . translate("Pour des raisons de sécurité, votre nom d'utilisateur et votre adresse IP vont être momentanément conservés.") . '<br />' . translate("Merci pour cette information. Nous allons l'examiner dès que possible.") . '</p>
                </div>';

                include("footer.php");
            } else {
                Header('Location: '. site_url('download.php'));
            }
        } else {
            Header('Location: '. site_url('download.php'));
        }
    }

}

switch (Request::input('op')) 
{
    case 'main':
        controllerSart(DownloadController::class, 'main');
        break;

    case 'mydown':
        controllerSart(DownloadController::class, 'transferfile');
        break;

    case 'broken':
        controllerSart(DownloadController::class, 'broken');
        break;

    default:
        controllerSart(DownloadController::class, 'main');
        break;
}
