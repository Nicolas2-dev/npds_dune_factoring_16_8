<?php


if (strstr($bnid, '..') 
|| strstr($bnid, './') 
|| stristr($bnid, 'script') 
|| stristr($bnid, 'cookie') 
|| stristr($bnid, 'iframe') 
|| stristr($bnid, 'applet') 
|| stristr($bnid, 'object') 
|| stristr($bnid, 'meta')) {
    die();
}

$result = sql_query("SELECT texte 
                     FROM " . sql_table('blocnotes') . " 
                     WHERE bnid='$bnid'");

if (sql_num_rows($result) > 0) {
    list($texte) = sql_fetch_row($result);

    $texte = stripslashes($texte);
    $texte = str_replace(chr(13) . chr(10), "\\n", str_replace("'", "\'", $texte));
    
    echo '$(function(){ $("#texteBlocNote_' . $bnid . '").val(unescape("' . str_replace('"', '\\"', $texte) . '")); })';
}
