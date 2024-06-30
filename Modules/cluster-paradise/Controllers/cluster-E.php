<?php

use Npds\Support\Facades\Log;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Crypt;

/**
 * [V_secur_cluster description]
 *
 * @param   [type]  $Xkey  [$Xkey description]
 *
 * @return  [type]         [return description]
 */
function V_secur_cluster($Xkey)
{
    global $ModPath;

    $ModPath = str_replace('..', '', $ModPath);
    $trouve = false;

    if (file_exists("modules/$ModPath/Config/data-cluster-E.php")) {
        include("modules/$ModPath/Config/data-cluster-E.php");
        $cpt = 0;

        // Note a revoir le each !!!!
        while (each($part) and !$trouve) {

            if (md5($part[$cpt]["WWW"] . $part[$cpt]["KEY"]) == Crypt::decryptK($Xkey, $part[$cpt]["KEY"]))
            {
                $trouve = true;
            } else {
                $cpt = $cpt + 1;
            }
        }
    }

    if ($trouve) {
        return $part[$cpt];
    } else {
        return false;
    }
}

if ($tmp = V_secur_cluster($key)) {
    if (($Xop == "NEWS") and ($tmp['SUBSCRIBE'] == "NEWS") and ($tmp['OP'] == "IMPORT")) {

        // vérifie que le membre existe bien sur le site
        $author = Crypt::decryptK(Hack::removeHack($Xauthor), $tmp['KEY']);
        $result = sql_query("SELECT name 
                             FROM " . sql_table('users') . " 
                             WHERE uname='$author'");

        list($name) = sql_fetch_row($result);

        if ($name == $author) {
            $pasfinA = true;
        } else {
            $pasfinA = false;
        }

        // vérifie que le l'auteur existe bien et ne dispose que des droits minimum
        $aid = Crypt::decryptK(Hack::removeHack($Xaid), $tmp['KEY']);

        $result = sql_query("SELECT radminarticle 
                             FROM " . sql_table('authors') . " 
                             WHERE aid='$aid'");

        list($radminarticle) = sql_fetch_row($result);

        if ($radminarticle == 1) {
            $pasfinB = true;
        } else {
            $pasfinB = false;
        }

        // vérifie que la catégorie existe : sinon met la catégorie générique
        $catid = Crypt::decryptK(Hack::removeHack($Xcatid), $tmp['KEY']);

        $result = sql_query("SELECT catid 
                             FROM " . sql_table('stories_cat') . " 
                             WHERE title='" . addslashes($catid) . "'");

        list($catid) = sql_fetch_row($result);

        // vérifie que le Topic existe : sinon met le Topic générique
        $topic = Crypt::decryptK(Hack::removeHack($Xtopic), $tmp['KEY']);

        $result = sql_query("SELECT topicid 
                             FROM " . sql_table('topics') . " 
                             WHERE topictext='" . addslashes($topic) . "'");

        list($topicid) = sql_fetch_row($result);

        // OK on fait la mise à jour
        if ($pasfinA and $pasfinB) {

            $subject        = Crypt::decryptK(Hack::removeHack($Xsubject), $tmp['KEY']);
            $hometext       = Crypt::decryptK(Hack::removeHack($Xhometext), $tmp['KEY']);
            $bodytext       = Crypt::decryptK(Hack::removeHack($Xbodytext), $tmp['KEY']);
            $notes          = Crypt::decryptK(Hack::removeHack($Xnotes), $tmp['KEY']);
            $ihome          = Crypt::decryptK(Hack::removeHack($Xihome), $tmp['KEY']);
            $date_finval    = Crypt::decryptK(Hack::removeHack($Xdate_finval), $tmp['KEY']);
            $epur           = Crypt::decryptK(Hack::removeHack($Xepur), $tmp['KEY']);

            // autonews ou pas ?
            $date_debval = Crypt::decryptK(Hack::removeHack($Xdate_debval), $tmp['KEY']);

            if ($date_debval == '') {
                $result = sql_query("INSERT 
                                     INTO " . sql_table('stories') . " 
                                     VALUES (NULL, '$catid', '$aid', '$subject', now(), '$hometext', '$bodytext', '0', '0', '$topicid', '$author', '$notes', '$ihome', '0', '$date_finval','$epur')");

                Log::Ecr_Log("security", "Cluster Paradise : insert_stories ($subject - $date_finval) by AID : $aid", "");
                
                // Réseaux sociaux
                if (file_exists('modules/npds_twi/npds_to_twi.php')) {
                    include('modules/npds_twi/npds_to_twi.php');
                }

                if (file_exists('modules/npds_fbk/npds_to_fbk.php')) {
                    include('modules/npds_twi/npds_to_fbk.php');
                }
                // Réseaux sociaux
            } else {
                $result = sql_query("INSERT 
                                     INTO " . sql_table('autonews') . " 
                                     VALUES (NULL, '$catid', '$aid', '$subject', now(), '$hometext', '$bodytext', '$topicid', '$author', '$notes', '$ihome','$date_debval','$date_finval','$epur')");

                Log::Ecr_Log("security", "Cluster Paradise : insert_autonews ($subject - $date_debval - $date_finval) by AID : $aid", "");
            }

            sql_query("UPDATE " . sql_table('users') . " 
                       SET counter=counter+1 
                       WHERE uname='$author'");

            sql_query("UPDATE " . sql_table('authors') . " 
                       SET counter=counter+1 
                       WHERE aid='$aid'");
        }
    }
}

echo '
    <script type="text/javascript">
        //<![CDATA[
            self.close();
        //]]>
    </script>';
