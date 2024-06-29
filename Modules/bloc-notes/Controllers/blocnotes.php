<?php

use Npds\Support\Facades\Hack;


if ($uriBlocNote) {
    if ($typeBlocNote == "shared")
        $bnid = md5($nomBlocNote);
    elseif ($typeBlocNote == "context") {
        if ($nomBlocNote == "\$username") {

            global $cookie, $admin;
            $nomBlocNote = $cookie[1];

            $cur_admin = explode(':', base64_decode($admin));

            if ($cur_admin)
                $nomBlocNote = $cur_admin[0];
        }

        if (stristr(urldecode($uriBlocNote), "article.php"))
            $bnid = md5($nomBlocNote . substr(urldecode($uriBlocNote), 0, strpos(urldecode($uriBlocNote), "&")));
        else
            $bnid = md5($nomBlocNote . urldecode($uriBlocNote));
    } else
        $bnid = '';

    if ($bnid) {
        if ($supBlocNote == 'RAZ')
            sql_query("DELETE FROM " . sql_table('blocnotes') . " WHERE bnid='$bnid'");
        else {
            sql_query("LOCK TABLES " . sql_table('blocnotes') . " WRITE");
            $result = sql_query("SELECT texte FROM " . sql_table('blocnotes') . " WHERE bnid='$bnid'");

            if (sql_num_rows($result) > 0) {
                if ($texteBlocNote != '')
                    sql_query("UPDATE " . sql_table('blocnotes') . " SET texte='" . Hack::removeHack($texteBlocNote) . "' WHERE bnid='$bnid'");
                else
                    sql_query("DELETE FROM " . sql_table('blocnotes') . " WHERE bnid='$bnid'");
            } else {
                if ($texteBlocNote != '')
                    sql_query("INSERT INTO " . sql_table('blocnotes') . " (bnid, texte) VALUES ('$bnid', '" . Hack::removeHack($texteBlocNote) . "')");
            }

            sql_query("UNLOCK TABLES");
        }
    }
    header("location: " . urldecode($uriBlocNote));
} else
    header("location: index.php");
