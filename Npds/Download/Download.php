<?php

namespace Npds\Download;

use Npds\file\File;
use Npds\Config\Config;
use Npds\file\FileManagement;
use Npds\Support\Facades\Str;
use Npds\Support\Facades\Date;
use Npds\Support\Facades\Groupe;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Paginator;
use Npds\Contracts\Download\DownloadInterface;


/**
 * Download class
 */
class Download implements DownloadInterface
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
     * Bloc topdownload et lastdownload
     *
     * @param   [type]  $form   [$form description]
     * @param   [type]  $ordre  [$ordre description]
     *
     * @return  [type]          [return description]
     */
    public static function topDownloadData($form, $ordre)
    {
        global $long_chain;

        if (!$long_chain) {
            $long_chain = 13;
        }

        $top = Config::get('download.top');

        $result = sql_query("SELECT did, dcounter, dfilename, dcategory, ddate, perms 
                             FROM " . sql_table('downloads') . " 
                             ORDER BY $ordre 
                             DESC LIMIT 0, $top");

        $lugar = 1;
        $ibid = '';

        while (list($did, $dcounter, $dfilename, $dcategory, $ddate, $dperm) = sql_fetch_row($result)) {
            if ($dcounter > 0) {
                $okfile = Groupe::autorisation($dperm);

                if ($ordre == 'dcounter') {
                    $dd = Str::wrh($dcounter);
                }

                if ($ordre == 'ddate') {
                    $dd = translate("dateinternal");
                    $day = substr($ddate, 8, 2);
                    $month = substr($ddate, 5, 2);
                    $year = substr($ddate, 0, 4);
                    
                    $dd = str_replace('d', $day, $dd);
                    $dd = str_replace('m', $month, $dd);
                    $dd = str_replace('Y', $year, $dd);
                    $dd = str_replace("H:i", "", $dd);
                }

                $ori_dfilename = $dfilename;

                if (strlen($dfilename) > $long_chain) {
                    $dfilename = (substr($dfilename, 0, $long_chain)) . " ...";
                }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">
                            ' . $lugar . ' 
                            <a class="ms-2" href="download.php?op=geninfo&amp;did=' . $did . '&amp;out_template=1" title="' . $ori_dfilename . ' ' . $dd . '" >
                                ' . $dfilename . '
                            </a>
                            <span class="badge bg-secondary ms-auto align-self-center">' . $dd . '</span>
                        </li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1">
                            <a href="download.php?op=mydown&amp;did=' . $did . '" >
                                ' . $dfilename . '
                            </a> 
                            (' . translate("Catégorie") . ' : ' . Language::aff_langue(stripslashes($dcategory)) . ')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">' . Str::wrh($dcounter) . '</span>
                        </li>';
                    }
                }

                if ($okfile) {
                    $lugar++;
                }
            }
        }

        sql_free_result($result);

        return $ibid;
    }

    /**
     * [geninfo description]
     *
     * @param   [type]  $did           [$did description]
     * @param   [type]  $out_template  [$out_template description]
     *
     * @return  [type]                 [return description]
     */
    public static function geninfo($did, $out_template)
    {
        // settype($did, 'integer');
        // settype($out_template, 'integer');

        $result = sql_query("SELECT dcounter, durl, dfilename, dfilesize, ddate, dweb, duser, dver, dcategory, ddescription, perms 
                            FROM " . sql_table('downloads') . " 
                            WHERE did='$did'");

        list($dcounter, $durl, $dfilename, $dfilesize, $ddate, $dweb, $duser, $dver, $dcategory, $ddescription, $dperm) = sql_fetch_row($result);

        $okfile = false;
        if (!stristr($dperm, ',')) {
            $okfile = Groupe::autorisation($dperm);
        } else {
            $ibidperm = explode(',', $dperm);

            foreach ($ibidperm as $v) {
                if (Groupe::autorisation($v)) {
                    $okfile = true;
                    break;
                }
            }
        }

        if ($okfile) {
            //$title = $dfilename; // not used !!

            if ($out_template == 1) {
                include('header.php');

                echo '
                <h2 class="mb-3">' . translate("Chargement de fichiers") . '</h2>
                <div class="card">
                    <div class="card-header"><h4>' . $dfilename . '<span class="ms-3 text-body-secondary small">@' . $durl . '</h4></div>
                    <div class="card-body">';
            }

            echo '<p><strong>' . translate("Taille du fichier") . ' : </strong>';

            $objZF = new FileManagement;

            if ($dfilesize != 0) {
                echo $objZF->file_size_format($dfilesize, 1);
            } else {
                echo $objZF->file_size_auto($durl, 2);
            }

            echo '</p>
                    <p>
                    <strong>' . translate("Version") . '&nbsp;:</strong>&nbsp;' . $dver . '</p>
                    <p>
                    <strong>' . translate("Date de chargement sur le serveur") . '&nbsp;:</strong>&nbsp;' . Date::convertdate($ddate) . '</p>
                    <p>
                    <strong>' . translate("Chargements") . '&nbsp;:</strong>&nbsp;' . Str::wrh($dcounter) . '</p>
                    <p>
                    <strong>' . translate("Catégorie") . '&nbsp;:</strong>&nbsp;' . Language::aff_langue(stripslashes($dcategory)) . '</p>
                    <p>
                    <strong>' . translate("Description") . '&nbsp;:</strong>&nbsp;' . Language::aff_langue(stripslashes($ddescription)) . '</p>
                    <p>
                    <strong>' . translate("Auteur") . '&nbsp;:</strong>&nbsp;' . $duser . '</p>
                    <p><strong>' . translate("Page d'accueil") . '&nbsp;:</strong>&nbsp;<a href="http://' . $dweb . '" target="_blank">' . $dweb . '</a></p>';
            
            if ($out_template == 1) {
                echo '
                    <a class="btn btn-primary" href="'. site_url('download.php?op=mydown&amp;did=' . $did) . '" target="_blank" title="' . translate("Charger maintenant") . '" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa fa-lg fa-download"></i>
                    </a>
                    </div>
                </div>';

                include('footer.php');
            }
        } else {
            Header('Location: '. site_url('download.php'));
        }
    }

    /**
     * [tlist description]
     *
     * @return  [type]  [return description]
     */
    public static function tlist()
    {
        global $sortby, $dcategory;

        if ($dcategory == '') {
            $dcategory = addslashes(Config::get('download.download_cat'));
        }

        $cate = stripslashes($dcategory);

        echo '
        <p class="lead">' . translate("Sélectionner une catégorie") . '</p>
        <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between my-3 border rounded">
            <p class="p-2 mb-0 ">';

        $acounter = sql_query("SELECT COUNT(*) 
                               FROM " . sql_table('downloads'));

        list($acount) = sql_fetch_row($acounter);

        if (($cate == translate("Tous")) or ($cate == '')) {
            echo '<i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i>
            <strong>    
                <span class="align-middle">' . translate("Tous") . '</span>
                <span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span>
            </strong>';
        } else {
            echo '<a href="'. site_url('download.php?dcategory=' . translate("Tous") . '&amp;sortby=' . $sortby) . '">
                <i class="fa fa-folder fa-2x align-middle me-2"></i>
                <span class="align-middle">' . translate("Tous") . '</span>
            </a>
            <span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span>';
        }

        $result = sql_query("SELECT DISTINCT dcategory, COUNT(dcategory) 
                             FROM " . sql_table('downloads') . " 
                             GROUP BY dcategory 
                             ORDER BY dcategory");

        echo '</p>';

        while (list($category, $dcount) = sql_fetch_row($result)) {
            $category = stripslashes($category);

            echo '<p class="p-2 mb-0">';

            if ($category == $cate) {
                echo '<i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i>
                    <strong class="align-middle">
                        ' . Language::aff_langue($category) . '<span class="badge bg-secondary ms-2 float-end my-2">' . $dcount . '</span>
                    </strong>';
            } else {
                $category2 = urlencode($category);
                
                echo '<a href="'. site_url('download.php?dcategory=' . $category2 . '&amp;sortby=' . $sortby) . '">
                        <i class="fa fa-folder fa-2x align-middle me-2"></i>
                        <span class="align-middle">' . Language::aff_langue($category) . '</span>
                    </a>
                    <span class="badge bg-secondary ms-2 my-2 float-end">' . $dcount . '</span>';
            }

            echo '</p>';
        }

        echo '
        </div>';
    }

    /**
     * [act_dl_tableheader description]
     *
     * @param   [type]  $dcategory    [$dcategory description]
     * @param   [type]  $sortby       [$sortby description]
     * @param   [type]  $fieldname    [$fieldname description]
     * @param   [type]  $englishname  [$englishname description]
     *
     * @return  [type]                [return description]
     */
    public static function act_dl_tableheader($dcategory, $sortby, $fieldname, $englishname)
    {
        echo '
            <a class="d-none d-sm-inline" href="'. site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname) . '" title="' . translate("Croissant") . '" data-bs-toggle="tooltip" >
                <i class="fa fa-sort-amount-down"></i>
            </a>&nbsp;
            ' . translate("$englishname") . '&nbsp;
            <a class="d-none d-sm-inline" href="'. site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC') .'" title="' . translate("Décroissant") . '" data-bs-toggle="tooltip" >
                <i class="fa fa-sort-amount-up"></i>
            </a>';
    }

    /**
     * [inact_dl_tableheader description]
     *
     * @param   [type]  $dcategory    [$dcategory description]
     * @param   [type]  $sortby       [$sortby description]
     * @param   [type]  $fieldname    [$fieldname description]
     * @param   [type]  $englishname  [$englishname description]
     *
     * @return  [type]                [return description]
     */
    public static function inact_dl_tableheader($dcategory, $sortby, $fieldname, $englishname)
    {
        echo '
            <a class="d-none d-sm-inline" href="'. site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname) . '" title="' . translate("Croissant") . '" data-bs-toggle="tooltip">
                <i class="fa fa-sort-amount-down" ></i>
            </a>&nbsp;
            ' . translate("$englishname") . '&nbsp;
            <a class="d-none d-sm-inline" href="'. site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC') .'" title="' . translate("Décroissant") . '" data-bs-toggle="tooltip">
                <i class="fa fa-sort-amount-up" ></i>
            </a>';
    }

    /**
     * [dl_tableheader description]
     *
     * @return  [type]  [return description]
     */
    public static function dl_tableheader()
    {
        echo '</td>
        <td>';
    }

    /**
     * [popuploader description]
     *
     * @param   [type]  $did           [$did description]
     * @param   [type]  $ddescription  [$ddescription description]
     * @param   [type]  $dcounter      [$dcounter description]
     * @param   [type]  $dfilename     [$dfilename description]
     * @param   [type]  $aff           [$aff description]
     *
     * @return  [type]                 [return description]
     */
    public static function popuploader($did, $ddescription, $dcounter, $dfilename, $aff)
    {
        $out_template = 0;

        if ($aff) {
            echo '
                <a class="me-3" href="#" data-bs-toggle="modal" data-bs-target="#mo' . $did . '" title="' . translate("Information sur le fichier") . '" data-bs-toggle="tooltip"><i class="fa fa-info-circle fa-2x"></i></a>
                <a href="'. site_url('download.php?op=mydown&amp;did=' . $did) . '" target="_blank" title="' . translate("Charger maintenant") . '" data-bs-toggle="tooltip">
                    <i class="fa fa-download fa-2x"></i>
                </a>
                <div class="modal fade" id="mo' . $did . '" tabindex="-1" role="dialog" aria-labelledby="my' . $did . '" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title text-start" id="my' . $did . '">' . translate("Information sur le fichier") . ' - ' . $dfilename . '</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title=""></button>
                        </div>
                        <div class="modal-body text-start">';
            
            static::geninfo($did, $out_template);
            
            echo '
                        </div>
                        <div class="modal-footer">
                            <a class="" href="'. site_url('download.php?op=mydown&amp;did=' . $did) . '" title="' . translate("Charger maintenant") . '">
                                <i class="fa fa-2x fa-download"></i>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>';
        }
    }

    /**
     * [SortLinks description]
     *
     * @param   [type]  $dcategory  [$dcategory description]
     * @param   [type]  $sortby     [$sortby description]
     *
     * @return  [type]              [return description]
     */
    public static function SortLinks($dcategory, $sortby)
    {
        global $user;

        $dcategory = stripslashes($dcategory);

        echo '
            <thead>
                <tr>
                    <th class="text-center">' . translate("Fonctions") . '</th>
                    <th class="text-center n-t-col-xs-1" data-sortable="true" data-sorter="htmlSorter">' . translate("Type") . '</th>
                    <th class="text-center">';

        if ($sortby == 'dfilename' or !$sortby) {
            static::act_dl_tableheader($dcategory, $sortby, "dfilename", "Nom");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "dfilename", "Nom");
        }

        echo '</th>
                <th class="text-center">';

        if ($sortby == "dfilesize") {
            static::act_dl_tableheader($dcategory, $sortby, "dfilesize", "Taille");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "dfilesize", "Taille");
        }

        echo '</th>
                <th class="text-center">';

        if ($sortby == "dcategory") {
            static::act_dl_tableheader($dcategory, $sortby, "dcategory", "Catégorie");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "dcategory", "Catégorie");
        }

        echo '</th>
                <th class="text-center">';

        if ($sortby == "ddate") {
            static::act_dl_tableheader($dcategory, $sortby, "ddate", "Date");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "ddate", "Date");
        }

        echo '</th>
                <th class="text-center">';

        if ($sortby == "dver") {
            static::act_dl_tableheader($dcategory, $sortby, "dver", "Version");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "dver", "Version");
        }

        echo '</th>
                <th class="text-center">';

        if ($sortby == "dcounter") {
            static::act_dl_tableheader($dcategory, $sortby, "dcounter", "Compteur");
        } else {
            static::inact_dl_tableheader($dcategory, $sortby, "dcounter", "Compteur");
        }

        echo '</th>';

        if ($user or Groupe::autorisation(-127)) {
            echo '<th class="text-center n-t-col-xs-1"></th>';
        }

        echo '
                </tr>
            </thead>';
    }

    /**
     * [listdownloads description]
     *
     * @param   [type]  $dcategory  [$dcategory description]
     * @param   [type]  $sortby     [$sortby description]
     * @param   [type]  $sortorder  [$sortorder description]
     *
     * @return  [type]              [return description]
     */
    public static function listdownloads($dcategory, $sortby, $sortorder)
    {
        global $perpage, $page, $user;

        if ($dcategory == '') {
            $dcategory = addslashes(Config::get('download.download_cat'));
        }

        if (!$sortby) {
            $sortby = 'dfilename';
        }

        if (($sortorder != "ASC") && ($sortorder != "DESC")) {
            $sortorder = "ASC";
        }

        echo '<p class="lead">';

        echo translate("Affichage filtré pour") . "&nbsp;<i>";

        if ($dcategory == translate("Tous")) {
            echo '<b>' . translate("Tous") . '</b>';
        } else {
            echo '<b>' . Language::aff_langue(stripslashes($dcategory)) . '</b>';
        }

        echo '</i>&nbsp;' . translate("trié par ordre") . '&nbsp;';

        // Shiney SQL Injection 11/2011
        $sortby2 = '';

        if ($sortby == 'dfilename') {
            $sortby2 = translate("Nom") . "";
        }

        if ($sortby == 'dfilesize') {
            $sortby2 = translate("Taille du fichier") . "";
        }

        if ($sortby == 'dcategory') {
            $sortby2 = translate("Catégorie") . "";
        }

        if ($sortby == 'ddate') {
            $sortby2 = translate("Date de création") . "";
        }

        if ($sortby == 'dver') {
            $sortby2 = translate("Version") . "";
        }

        if ($sortby == 'dcounter') {
            $sortby2 = translate("Chargements") . "";
        }

        // Shiney SQL Injection 11/2011
        if ($sortby2 == '') {
            $sortby = 'dfilename';
        }

        echo translate("de") . '&nbsp;<i><b>' . $sortby2 . '</b></i>
        </p>';

        echo '
        <table class="table table-hover mb-3 table-sm" id ="lst_downlo" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true"
        data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">';

        static::sortlinks($dcategory, $sortby);

        echo '<tbody>';

        if ($dcategory == translate("Tous")) {
            $sql = "SELECT COUNT(*) 
                    FROM " . sql_table('downloads');
        } else {
            $sql = "SELECT COUNT(*) 
                    FROM " . sql_table('downloads') . " 
                    WHERE dcategory='" . addslashes($dcategory) . "'";
        }

        $result = sql_query($sql);
        list($total) =  sql_fetch_row($result);

        //
        if ($total > $perpage) {
            $pages = ceil($total / $perpage);
            if ($page > $pages) {
                $page = $pages;
            }

            if (!$page) {
                $page = 1;
            }

            $offset = ($page - 1) * $perpage;
        } else {
            $offset = 0;
            $pages = 1;
            $page = 1;
        }

        //  
        $nbPages = ceil($total / $perpage);
        $current = 1;

        if ($page >= 1) {
            $current = $page;
        } else if ($page < 1) {
            $current = 1;
        } else {
            $current = $nbPages;
        }

        // settype($offset, 'integer');
        // settype($perpage, 'integer');

        if ($dcategory == translate("Tous")) {
            $sql = "SELECT * 
                    FROM " . sql_table('downloads') . " 
                    ORDER BY $sortby $sortorder 
                    LIMIT $offset, $perpage";
        } else {
            $sql = "SELECT * 
                    FROM " . sql_table('downloads') . " 
                    WHERE dcategory='" . addslashes($dcategory) . "' 
                    ORDER BY $sortby $sortorder 
                    LIMIT $offset, $perpage";
        }

        $result = sql_query($sql);

        while (list($did, $dcounter, $durl, $dfilename, $dfilesize, $ddate, $dweb, $duser, $dver, $dcat, $ddescription, $dperm) = sql_fetch_row($result)) {

            $okfile = '';
            if (!stristr($dperm, ',')) {
                $okfile = Groupe::autorisation($dperm);
            } else {
                $ibidperm = explode(',', $dperm);

                foreach ($ibidperm as $v) {
                    if (Groupe::autorisation($v) == true) {
                        $okfile = true;
                        break;
                    }
                }
            }

            echo '
                <tr>
                    <td class="text-center">';

            if ($okfile == true) {
                echo static::popuploader($did, $ddescription, $dcounter, $dfilename, true);
            } else {
                echo static::popuploader($did, $ddescription, $dcounter, $dfilename, false);
                echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>' . translate("Privé") . '</span>';
            }

            $Fichier = new File($durl);

            echo '</td>
                <td class="text-center">' . $Fichier->Affiche_Extention('webfont') . '</td>
                <td>';

            if ($okfile == true) {
                echo '<a href="'. site_url('"download.php?op=mydown&amp;did=' . $did) . '" target="_blank">' . $dfilename . '</a>';
            } else {
                echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>...</span>';
            }

            echo '</td>
                <td class="small text-center">';

            $FichX = new FileManagement;

            if ($dfilesize != 0) {
                echo $FichX->file_size_format($dfilesize, 1);
            } else {
                echo $FichX->file_size_auto($durl, 2);
            }

            echo '</td>
                <td>' . Language::aff_langue(stripslashes($dcat)) . '</td>
                <td class="small text-center">' . Date::convertdate($ddate) . '</td>
                <td class="small text-center">' . $dver . '</td>
                <td class="small text-center">' . Str::wrh($dcounter) . '</td>';

            if ($user != '' or Groupe::autorisation(-127)) {
                echo '
                <td>';

                if (($okfile == true and $user != '') or Groupe::autorisation(-127)) {
                    echo '<a href="'. site_url('download.php?op=broken&amp;did=' . $did) . '" title="' . translate("Rapporter un lien rompu") . '" data-bs-toggle="tooltip">
                            <i class="fas fa-lg fa-unlink"></i>
                        </a>';
                }

                echo '
                </td>';
            }

            echo '
            </tr>';
        }

        echo '
            </tbody>
        </table>';

        $dcategory = StripSlashes($dcategory);

        echo '<div class="mt-3"></div>
            ' . Paginator::paginate_single(site_url('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $sortby . '&amp;sortorder=' . $sortorder . '&amp;page='), '', $nbPages, $current, $adj = 3, '', $page);
    }

}