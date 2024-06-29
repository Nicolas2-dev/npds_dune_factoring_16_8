<?php

use Npds\Language\Language;
use Npds\Support\Facades\Log;
use Npds\Support\Facades\Str;


if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'blocks';

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

global $language;
$hlpfile = "manuels/$language/leftblocks.html";

/**
 * [makelblock description]
 *
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Lindex   [$Lindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $BLaide   [$BLaide description]
 * @param   [type]  $SHTML    [$SHTML description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function makelblock($title, $content, $members, $Mmember, $Lindex, $Scache, $BLaide, $SHTML, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) {
        $Lindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));
    $content = stripslashes(Str::FixQuotes($content));

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', '\n', $content));
    }

    sql_query("INSERT 
               INTO " . sql_table('lblocks') . " 
               VALUES (NULL,'$title','$content','$members', '$Lindex', '$Scache', '1','$css', '$BLaide')");

    global $aid;
    Log::Ecr_Log('security', "MakeLeftBlock(" . Language::aff_langue($title) . ") by AID : $aid", "");

    Header("Location: admin.php?op=blocks");
}

/**
 * [changelblock description]
 *
 * @param   [type]  $id       [$id description]
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Lindex   [$Lindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $Sactif   [$Sactif description]
 * @param   [type]  $BLaide   [$BLaide description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function changelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) {
        $Lindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    if ($css) {
        $css = 1;
    } else {
        $css = 0;
    }

    $content = stripslashes(Str::FixQuotes($content));
    $BLaide = stripslashes(Str::FixQuotes($BLaide));

    sql_query("UPDATE " . sql_table('lblocks') . " 
               SET title='$title', content='$content', member='$members', Lindex='$Lindex', cache='$Scache', actif='$Sactif', aide='$BLaide', css='$css' 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "ChangeLeftBlock(" . Language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [changedroitelblock description]
 *
 * @param   [type]  $id       [$id description]
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 * @param   [type]  $members  [$members description]
 * @param   [type]  $Mmember  [$Mmember description]
 * @param   [type]  $Lindex   [$Lindex description]
 * @param   [type]  $Scache   [$Scache description]
 * @param   [type]  $Sactif   [$Sactif description]
 * @param   [type]  $BLaide   [$BLaide description]
 * @param   [type]  $css      [$css description]
 *
 * @return  [type]            [return description]
 */
function changedroitelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Lindex)) {
        $Lindex = 0;
    }

    $title = stripslashes(Str::FixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    if ($css) { 
        $css = 1;
    } else {
        $css = 0;
    }

    $content = stripslashes(Str::FixQuotes($content));
    $BLaide = stripslashes(Str::FixQuotes($BLaide));

    sql_query("INSERT 
               INTO " . sql_table('rblocks') . " 
               VALUES (NULL,'$title','$content', '$members', '$Lindex', '$Scache', '$Sactif', '$css', '$BLaide')");

    sql_query("DELETE 
               FROM " . sql_table('lblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "MoveLeftBlockToRight(" . Language::aff_langue($title) . " - $id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

/**
 * [deletelblock description]
 *
 * @param   [type]  $id  [$id description]
 *
 * @return  [type]       [return description]
 */
function deletelblock($id)
{
    sql_query("DELETE 
               FROM " . sql_table('lblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::Ecr_Log('security', "DeleteLeftBlock($id) by AID : $aid", '');

    Header("Location: admin.php?op=blocks");
}

// settype($css, 'integer');
$Mmember = isset($Mmember) ? $Mmember : '';
// settype($Sactif, 'string');
// settype($SHTML, 'string');

switch ($op) {
    
    case 'makelblock':
        makelblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deletelblock':
        deletelblock($id);
        break;

    case 'changelblock':
        changelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
        break;

    case 'droitelblock':
        changedroitelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
        break;
}
