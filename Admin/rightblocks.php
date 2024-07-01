<?php

use Npds\Support\Facades\Log;
use Npds\Support\Facades\Str;
use Npds\Support\Facades\Language;


if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'blocks';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language;
$hlpfile = "manuels/$language/rightblocks.html";

/**
 * [makerblock description]
 *
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Rindex   [$Rindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $BRaide   [$BRaide description]
 * @param   [type]  $SHTML    [$SHTML description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function makerblock($title, $content, $members, $Mmember, $Rindex, $Scache, $BRaide, $SHTML, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) { 
        $Rindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));
    $content = stripslashes(Str::FixQuotes($content));

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', "\n", $content));
    }

    sql_query("INSERT 
               INTO " . sql_table('rblocks') . " 
               VALUES (NULL,'$title','$content', '$members', '$Rindex', '$Scache', '1', '$css', '$BRaide')");

    global $aid;
    Log::Ecr_Log('security', "MakeRightBlock(" . Language::aff_langue($title) . ") by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [changerblock description]
 *
 * @param   [type]  $id       [$id description]
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Rindex   [$Rindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $Sactif   [$Sactif description]
 * @param   [type]  $BRaide   [$BRaide description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(Str::FixQuotes($content));
    sql_query("UPDATE " . sql_table('rblocks') . " 
               SET title='$title', content='$content', member='$members', Rindex='$Rindex', cache='$Scache', actif='$Sactif', css='$css', aide='$BRaide' 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "ChangeRightBlock(" . Language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [changegaucherblock description]
 *
 * @param   [type]  $id       [$id description]
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Rindex   [$Rindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $Sactif   [$Sactif description]
 * @param   [type]  $BRaide   [$BRaide description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));

    if ($Sactif == 'ON') { 
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(Str::FixQuotes($content));

    sql_query("INSERT 
               INTO " . sql_table('lblocks') . " 
               VALUES (NULL,'$title','$content','$members', '$Rindex', '$Scache', '$Sactif', '$css', '$BRaide')");

    sql_query("DELETE 
               FROM " . sql_table('rblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "MoveRightBlockToLeft(" . Language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}
/**
 * [deleterblock description]
 *
 * @param   [type]  $id  [$id description]
 *
 * @return  [type]       [return description]
 */
function deleterblock($id)
{
    sql_query("DELETE 
               FROM " . sql_table('rblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "DeleteRightBlock($id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

// settype($css, 'integer');
$Mmember = isset($Mmember) ? $Mmember : '';
// settype($Sactif, 'string');
// settype($SHTML, 'string');

switch ($op) {
    
    case 'makerblock':
        makerblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deleterblock':
        deleterblock($id);
        break;

    case 'changerblock':
        changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;

    case 'gaucherblock':
        changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;
}
