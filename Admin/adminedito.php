<?php

use Npds\Editeur\Editeur;
use Npds\Routing\Controller;
use Npds\Support\Facades\Css;
use Npds\Support\Facades\Log;
use Npds\Support\Facades\Url;
use Npds\Support\Facades\Request;


if (!function_exists('admindroits')) {
    include('die.php');
}


class AdminEditoController extends Controller
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

        $this->hlpfile = '/manuels/' . $language . '/edito.html';

        $this->f_titre = translate("Edito");

        $this->f_meta_nom = 'edito';

        //==> controle droit
        admindroits($aid, $this->f_meta_nom);
        //<== controle droit
    }

    /**
     * [edito description]
     *
     * @param   [type]  $edito_type  [$edito_type description]
     * @param   [type]  $contents    [$contents description]
     * @param   [type]  $Xaff_jours  [$Xaff_jours description]
     * @param   [type]  $Xaff_jour   [$Xaff_jour description]
     * @param   [type]  $Xaff_nuit   [$Xaff_nuit description]
     *
     * @return  [type]               [return description]
     */
    function edito()
    {
        include("header.php");

        GraphicAdmin($this->hlpfile);
        adminhead($this->f_meta_nom, $this->f_titre);

        echo '<hr />';

        if ($edito_type = Request::input('edito_type')) {
            if ($edito_type == 'G') {
                if (file_exists('storage/static/edito.txt')) {
                    $fp = fopen('storage/static/edito.txt', 'r');
                    if (filesize('storage/static/edito.txt') > 0) {
                        $Xcontents = fread($fp, filesize('storage/static/edito.txt'));
                    }
                    fclose($fp);
                }
            } elseif ($edito_type == 'M') {
                if (file_exists('storage/static/edito_membres.txt')) {
                    $fp = fopen('storage/static/edito_membres.txt', 'r');
                    if (filesize('storage/static/edito_membres.txt') > 0) {
                        $Xcontents = fread($fp, filesize('storage/static/edito_membres.txt'));
                    } else {
                        $Xcontents = '';
                    }
                    fclose($fp);
                }
            }

            if ($Xcontents == '') {
                $Xcontents = 'Edito ...';
                $contentJ = '';
                $contentN = '';
            } else {
                $Xcontents = preg_replace('#<!--|/-->#', '', $Xcontents);
            }

            $ibid = strstr($Xcontents, 'aff_jours');
            
            parse_str($ibid, $Xibidout);

            if (isset($Xibidout['aff_jours'])) {
                $Xcontents = substr($Xcontents, 0, strpos($Xcontents, 'aff_jours'));
                $Xaff_jours = $Xibidout['aff_jours'];
                $Xaff_jour = $Xibidout['aff_jour'];
                $Xaff_nuit = $Xibidout['aff_nuit'];
            } else {
                $Xaff_jours = 20;
                $Xaff_jour = 'checked="checked"';
                $Xaff_nuit = 'checked="checked"';
            }

            if (strpos($Xcontents, '[/jour]') > 0) {
                $contentJ = substr($Xcontents, strpos($Xcontents, '[jour]') + 6, strpos($Xcontents, '[/jour]') - 6);
                $contentN = substr($Xcontents, strpos($Xcontents, '[nuit]') + 6, strpos($Xcontents, '[/nuit]') - 19 - strlen($contentJ));
            }

            if (!$contentJ and !$contentN and !strpos($Xcontents, '[/jour]')) {
                $contentJ = $Xcontents;
            }               

            if ($edito_type == 'G') {
                $edito_typeL = ' ' . adm_translate("Anonyme");
            } elseif ($edito_type == 'M') {
                $edito_typeL = ' ' . adm_translate("Membre");
            }

            echo '
            <form id="admineditomod" action="admin.php" method="post" name="adminForm">
                <fieldset>
                <legend>' . adm_translate("Edito") . ' :' . $edito_typeL . '</legend>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="XeditoJ">' . adm_translate("Le jour") . '</label>
                    <div class="col-sm-12">
                    <textarea class="tin form-control" name="XeditoJ" rows="20" >';

            echo htmlspecialchars($contentJ, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, cur_charset);

            echo '</textarea>
                </div>
            </div>';

            echo Editeur::fetch('XeditoJ', '');

            echo '
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="XeditoN">' . adm_translate("La nuit") . '</label>';

            echo Editeur::fetch('XeditoN', '');

            echo '
                <div class="col-sm-12">
                <textarea class="tin form-control" name="XeditoN" rows="20">';

            echo htmlspecialchars($contentN, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, cur_charset);

            echo '</textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label" for="aff_jours">' . adm_translate("Afficher pendant") . '</label>
                    <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">' . adm_translate("jour(s)") . '</span>
                        <input class="form-control" type="number" name="aff_jours" id="aff_jours" min="0" step="1" max="999" value="' . $Xaff_jours . '" data-fv-digits="true" required="required" />
                    </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aff_jour" name="aff_jour" value="checked" ' . $Xaff_jour . ' />
                        <label class="form-check-label" for="aff_jour">' . adm_translate("Le jour") . '</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aff_nuit" name="aff_nuit" value="checked" ' . $Xaff_nuit . ' />
                        <label class="form-check-label" for="aff_nuit">' . adm_translate("La nuit") . '</label>
                    </div>
                    </div>
                </div>
            <input type="hidden" name="op" value="Edito_save" />
            <input type="hidden" name="edito_type" value="' . $edito_type . '" />
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto ">
                    <button class="btn btn-primary col-12" type="submit" name="edito_confirm"><i class="fa fa-check fa-lg"></i>&nbsp;' . adm_translate("Sauver les modifications") . ' </button>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto ">
                    <a href="admin.php?op=Edito" class="btn btn-secondary col-12">' . adm_translate("Abandonner") . '</a>
                </div>
            </div>
            </fieldset>
            </form>';

            $arg1 = '
            var formulid = ["admineditomod"];
            ';

            $fv_parametres = '
                aff_jours: {
                validators: {
                    digits: {
                        message: "This must be a number"
                    }
                }
            },';

            Css::adminfoot('fv', $fv_parametres, $arg1, '');

        } else {
            echo '
            <form id="fad_edi_choix" action="admin.php?op=Edito_load" method="post">
                <fieldset>
                    <legend>' . adm_translate("Type d'éditorial") . '</legend>
                    <div class="mb-3">
                    <select class="form-select" name="edito_type" onchange="submit()">
                        <option value="0">' . adm_translate("Modifier l'Editorial") . ' ...</option>
                        <option value="G">' . adm_translate("Anonyme") . '</option>
                        <option value="M">' . adm_translate("Membre") . '</option>
                    </select>
                    </div>
                </fieldset>
            </form>';

            Css::adminfoot('', '', '', '');
        }
    }

    /**
     * [edito_mod_save description]
     *
     * @param   [type]  $edito_type  [$edito_type description]
     * @param   [type]  $XeditoJ     [$XeditoJ description]
     * @param   [type]  $XeditoN     [$XeditoN description]
     * @param   [type]  $aff_jours   [$aff_jours description]
     * @param   [type]  $aff_jour    [$aff_jour description]
     * @param   [type]  $aff_nuit    [$aff_nuit description]
     *
     * @return  [type]               [return description]
     */
    function edito_mod_save()
    {
        $edito_type     = Request::input('edito_type');
        $XeditoJ        = Request::input('XeditoJ');
        $XeditoN        = Request::input('XeditoN');
        $aff_jours      = Request::input('aff_jours');
        $aff_jour       = Request::input('aff_jour');
        $aff_nuit       = Request::input('aff_nuit');

        if ($aff_jours <= 0) {
            $aff_jours = '999';
        }

        if ($edito_type == 'G') {
            $fp = fopen("storage/static/edito.txt", "w");
            fputs($fp, "[jour]" . str_replace('&quot;', '"', stripslashes($XeditoJ)) . '[/jour][nuit]' . str_replace('&quot;', '"', stripslashes($XeditoN)) . '[/nuit]');
            fputs($fp, 'aff_jours=' . $aff_jours);
            fputs($fp, '&aff_jour=' . $aff_jour);
            fputs($fp, '&aff_nuit=' . $aff_nuit);
            fputs($fp, '&aff_date=' . time());
            fclose($fp);
        } elseif ($edito_type == 'M') {
            $fp = fopen('storage/static/edito_membres.txt', 'w');
            fputs($fp, '[jour]' . str_replace('&quot;', '"', stripslashes($XeditoJ)) . '[/jour][nuit]' . str_replace('&quot;', '"', stripslashes($XeditoN)) . '[/nuit]');
            fputs($fp, 'aff_jours=' . $aff_jours);
            fputs($fp, '&aff_jour=' . $aff_jour);
            fputs($fp, '&aff_nuit=' . $aff_nuit);
            fputs($fp, '&aff_date=' . time());
            fclose($fp);
        }

        global $aid;
        Log::Ecr_Log('security', "editoSave () by AID : $aid", '');

        Url::redirect_url('admin.php?op=Edito');
    }

}

switch (Request::input('op')) {

    case 'Edito_save':
        controllerSart(AdminEditoController::class, 'edito_mod_save');
        break;

    case 'Edito_load':
        controllerSart(AdminEditoController::class, 'edito');
        break;

    default:
        controllerSart(AdminEditoController::class, 'edito');
        break;
}
