<?php

use Npds\Support\Facades\Css;
use Npds\Support\Facades\Log;
use Npds\Support\Facades\Str;


if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'ablock';
$f_titre = adm_translate("Bloc Administration");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language;
$hlpfile = "manuels/$language/adminblock.html";

/**
 * [ablock description]
 *
 * @return  [type]  [return description]
 */
function ablock()
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    include("header.php");

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '
        <hr />
        <h3 class="mb-3">' . adm_translate("Editer le Bloc Administration") . '</h3>';

    $result = sql_query("SELECT title, content 
                         FROM " . sql_table('block') . " 
                         WHERE id=2");

    if (sql_num_rows($result) > 0) {
        while (list($title, $content) = sql_fetch_row($result)) {
            echo '
            <form id="adminblock" action="admin.php" method="post" class="needs-validation">
                <div class="form-floating mb-3">
                <textarea class="form-control" type="text" name="title" id="title" maxlength="1000" style="height:70px;">' . $title . '</textarea>
                <label for="title">' . adm_translate("Titre") . '</label>
                <span class="help-block text-end"><span id="countcar_title"></span></span>
                </div>
                <div class="form-floating mb-3">
                <textarea class="form-control" type="text" rows="25" name="content" id="content" style="height:170px;">' . $content . '</textarea>
                <label for="content">' . adm_translate("Contenu") . '</label>
                </div>
                <input type="hidden" name="op" value="changeablock" />
                <button class="btn btn-primary btn-block" type="submit">' . adm_translate("Valider") . '</button>
            </form>';

            $arg1 = '
            var formulid = ["adminblock"];
            inpandfieldlen("title",1000);';
        }
    }

    Css::adminfoot('fv', '', $arg1, '');
}

/**
 * [changeablock description]
 *
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 *
 * @return  [type]            [return description]
 */
function changeablock($title, $content)
{
    $title = stripslashes(Str::FixQuotes($title));
    $content = stripslashes(Str::FixQuotes($content));

    sql_query("UPDATE " . sql_table('block') . " 
               SET title='$title', content='$content' 
               WHERE id='2'");

    global $aid;
    Log::Ecr_Log('security', "ChangeAdminBlock() by AID : $aid", '');

    Header("Location: admin.php?op=adminMain");
}

switch ($op) {

    case 'ablock':
        ablock();
        break;

    case 'changeablock':
        changeablock($title, $content);
        break;
}
