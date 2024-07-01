<?php

use Npds\Routing\Controller;
use Npds\Support\Facades\Css;
use Npds\Support\Facades\Str;
use Npds\Support\Facades\Url;
use Npds\Support\Facades\Stat;
use Npds\Support\Facades\Error;
use Npds\Support\Facades\Forum;
use Npds\Support\Facades\Request;

if (!stristr($_SERVER['PHP_SELF'], 'admin.php')) {
    include("admin/die.php");
}

if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

include("auth.php");


class AdminAblaLogController extends Controller
{

    /**
     * [$f_meta_nom description]
     *
     * @var [type]
     */
    private $f_meta_nom;

    /**
     * [$f_titre description]
     *
     * @var [type]
     */
    private $f_titre;    

    /**
     * [$hlpfile description]
     *
     * @var [type]
     */
    private $hlpfile;


    /**
     * [__construct description]
     *
     * @return  [type]  [return description]
     */
    public function __construct()
    {
        global $aid, $language;

        $this->hlpfile = '/manuels/' . $language . '/abla.html';

        $this->f_titre = translate("Tableau de bord");

        $this->f_meta_nom = 'abla';

        //==> controle droit
        admindroits($aid, $this->f_meta_nom);
        //<== controle droit
    }

    /**
     * [index description]
     *
     * @return  [type]  [return description]
     */
    protected function index()
    {   
        global $admin;

        if ($admin) {

            include("header.php");

            GraphicAdmin($this->hlpfile);
            adminhead($this->f_meta_nom, $this->f_titre);

            global $startdate;
            list($membres, $totala, $totalb, $totalc, $totald, $totalz) = Stat::req_stat();

            //LNL Email in outside table
            $result = sql_query("SELECT email FROM " . sql_table('lnl_outside_users') . "");

            if ($result) {
                $totalnl = sql_num_rows($result);
            } else {
                $totalnl = "0";
            }

            include("storage/ablalog/log.php");

            $timex = time() - $xdate;
            if ($timex >= 86400) {
                $timex = round($timex / 86400) . ' ' . translate("Jour(s)");
            } elseif ($timex >= 3600) {
                $timex = round($timex / 3600) . ' ' . translate("Heure(s)");
            } elseif ($timex >= 60) {
                $timex = round($timex / 60) . ' ' . translate("Minute(s)");
            } else {
                $timex = $timex . ' ' . translate("Seconde(s)");
            }

            echo '
            <hr />
            <p class="lead mb-3">' . translate("Statistiques générales") . ' - ' . translate("Dernières stats") . ' : ' . $timex . ' </p>
            <table class="mb-2" data-toggle="table" data-classes="table mb-2">
                <thead class="collapse thead-default">
                    <tr>
                        <th class="n-t-col-xs-9"></th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            ' . translate("Nb. pages vues") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totalz) . ' (';

            if ($totalz > $xtotalz) {
                echo '<span class="text-success">+';
            } elseif ($totalz < $xtotalz) {
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totalz - $xtotalz) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb. de membres") . ' : 
                        </td>
                        <td>
                            ' .Str::wrh($membres) . ' (';

            if ($membres > $xmembres){
                echo '<span class="text-success">+';
            } elseif ($membres < $xmembres){
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($membres - $xmembres) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb. d'articles") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totala) . ' (';

            if ($totala > $xtotala) {
                echo '<span class="text-success">+';
            } elseif ($totala < $xtotala){
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totala - $xtotala) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb. de forums") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totalc) . ' (';

            if ($totalc > $xtotalc) {
                echo '<span class="text-success">+';
            } elseif ($totalc < $xtotalc) {
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totalc - $xtotalc) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb. de sujets") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totald) . ' (';

            if ($totald > $xtotald) {
                echo '<span class="text-success">+';
            } elseif ($totald < $xtotald) {
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totald - $xtotald) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb. de critiques") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totalb) . ' (';

            if ($totalb > $xtotalb) {
                echo '<span class="text-success">+';
            } elseif ($totalb < $xtotalb) {
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totalb - $xtotalb) . '</span>)</td>
                    </tr>
                    <tr>
                        <td>
                            ' . translate("Nb abonnés à lettre infos") . ' : 
                        </td>
                        <td>
                            ' . Str::wrh($totalnl) . ' (';

            if ($totalnl > $xtotalnl) {
                echo '<span class="text-success">+';
            } elseif ($totalnl < $xtotalnl) {
                echo '<span class="text-danger">';
            } else {
                echo '<span>';
            }

            echo Str::wrh($totalnl - $xtotalnl) . '</span>)</td>
                    </tr>';

            $xfile = "<?php\n";
            $xfile .= "\$xdate = " . time() . ";\n";
            $xfile .= "\$xtotalz = $totalz;\n";
            $xfile .= "\$xmembres = $membres;\n";
            $xfile .= "\$xtotala = $totala;\n";
            $xfile .= "\$xtotalc = $totalc;\n";
            $xfile .= "\$xtotald = $totald;\n";
            $xfile .= "\$xtotalb = $totalb;\n";
            $xfile .= "\$xtotalnl = $totalnl;\n";

            echo '
                </tbody>
            </table>
            <p class="lead my-3">' . translate("Statistiques des chargements") . '</p>
            <table data-toggle="table" data-classes="table">
                <thead class=" thead-default">
                    <tr>
                        <th class="n-t-col-xs-9"></th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>';

            $num_dow = 0;
            $result = sql_query("SELECT dcounter, dfilename FROM " . sql_table('downloads'));

            // settype($xdownload, 'array');

            while (list($dcounter, $dfilename) = sql_fetch_row($result)) {
                $num_dow++;

                echo '
                    <tr>
                        <td>
                            <span class="text-danger">';

                if (array_key_exists($num_dow, $xdownload)) {
                    echo $xdownload[$num_dow][1];
                }

                echo '</span>
                    -/- ' . $dfilename . '
                    </td>
                    <td>
                        <span class="text-danger">';

                if (array_key_exists($num_dow, $xdownload)) {
                    echo $xdownload[$num_dow][2];
                }

                echo '</span>
                    -/- ' . $dcounter . '
                    </td>
                </tr>';

                $xfile .= "\$xdownload[$num_dow][1] = \"$dfilename\";\n";
                $xfile .= "\$xdownload[$num_dow][2] = \"$dcounter\";\n";
            }

            echo '
                </tbody>
            </table>
            <p class="lead my-3">Forums</p>
            <table class="table table-bordered table-sm" data-classes="table">
                <thead class="">
                    <tr>
                        <th>
                            ' . translate("Forum") . '
                        </th>
                        <th class="n-t-col-xs-2 text-center">
                            ' . translate("Sujets") . '
                        </th>
                        <th class="n-t-col-xs-2 text-center">
                            ' . translate("Contributions") . '
                        </th>
                        <th class="n-t-col-xs-3 text-end">
                            ' . translate("Dernières contributions") . '
                        </th>
                    </tr>
                </thead>';

            $result = sql_query("SELECT * FROM " . sql_table('catagories') . " ORDER BY cat_id");
            $num_for = 0;

            while (list($cat_id, $cat_title) = sql_fetch_row($result)) {
                $sub_sql = "SELECT f.*, u.uname 
                            FROM " . sql_table('forums') . " f, " . sql_table('users') . " u 
                            WHERE f.cat_id = '$cat_id' 
                            AND f.forum_moderator = u.uid 
                            ORDER BY forum_index, forum_id";
                
                if (!$sub_result = sql_query($sub_sql)) {
                    Error::code('0022');
                }
                
                if ($myrow = sql_fetch_assoc($sub_result)) {
                    echo '
                    <tbody>
                        <tr>
                            <td class="table-active" colspan="4">
                                ' . stripslashes($cat_title) . '
                            </td>
                        </tr>';

                    do {
                        $num_for++;
                        $last_post = Forum::get_last_post($myrow['forum_id'], 'forum', 'infos', true);

                        echo '<tr>';

                        $total_topics = Forum::get_total_topics($myrow['forum_id']);
                        $name = stripslashes($myrow['forum_name']);

                        $xfile .= "\$xforum[$num_for][1] = \"$name\";\n";
                        $xfile .= "\$xforum[$num_for][2] = $total_topics;\n";

                        $desc = stripslashes($myrow['forum_desc']);

                        echo '<td>
                            <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="' . $desc . '">
                                <i class="far fa-lg fa-file-alt me-2"></i>
                            </a>
                            <a href="viewforum.php?forum=' . $myrow['forum_id'] . '" >
                                <span class="text-danger">';
                        
                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][1];
                        }

                        echo '</span> -/- ' . $name . ' </a></td>
                            <td class="text-center">
                                <span class="text-danger">';

                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][2];
                        }

                        echo '</span> -/- ' . $total_topics . '</td>';

                        $total_posts = Forum::get_total_posts($myrow['forum_id'], "", "forum", false);
                        $xfile .= "\$xforum[$num_for][3] = $total_posts;\n";

                        echo '<td class="text-center">
                            <span class="text-danger">';

                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][3];
                        }

                        echo '</span>
                            -/- ' . $total_posts . '
                        </td>
                        <td class="text-end small">
                            ' . $last_post . '
                        </td>';

                    } while ($myrow = sql_fetch_assoc($sub_result));
                }
            }

            echo '
                    </tr>
                </tbody>
            </table>';

            $file = fopen("storage/ablalog/log.php", "w");
            $xfile .= "?>\n";
            fwrite($file, $xfile);
            fclose($file);

            Css::adminfoot('', '', '', '');
        } else {
            Url::redirect_url("index.php");
        }
    }

}

switch (Request::input('op')) 
{
    default:
        controllerSart(AdminAblaLogController::class, 'index');
        break;
}