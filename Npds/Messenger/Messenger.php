<?php

namespace Npds\Messenger;

use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Error;
use Npds\Support\Facades\Mailer;
use Npds\Contracts\Asset\MessengerInterface;


/**
 * Messenger class
 */
class Messenger implements MessengerInterface
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
     * Insère un MI dans la base et le cas échéant envoi un mail
     *
     * @param   [type]  $to_userid    [$to_userid description]
     * @param   [type]  $image        [$image description]
     * @param   [type]  $subject      [$subject description]
     * @param   [type]  $from_userid  [$from_userid description]
     * @param   [type]  $message      [$message description]
     * @param   [type]  $copie        [$copie description]
     *
     * @return  [type]                [return description]
     */
    public static function writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie)
    {
        $res = sql_query("SELECT uid, user_langue FROM " . sql_table('users') . " WHERE uname='$to_userid'");
        list($to_useridx, $user_languex) = sql_fetch_row($res);

        if ($to_useridx == '') {
            Error::code('0016');
        } else {
            global $gmt;
            $time = date(translate("dateinternal"), time() + ((int)$gmt * 3600));

            include_once("Language/lang-multi.php");

            $subject = Hack::removeHack($subject);
            $message = str_replace("\n", "<br />", $message);
            $message = addslashes(Hack::removeHack($message));

            $sql = "INSERT INTO " . sql_table('priv_msgs') . " (msg_image, subject, from_userid, to_userid, msg_time, msg_text) ";
            $sql .= "VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message')";

            if (!$result = sql_query($sql)) {
                Error::code('0020');
            }

            if ($copie) {
                $sql = "INSERT INTO " . sql_table('priv_msgs') . " (msg_image, subject, from_userid, to_userid, msg_time, msg_text, type_msg, read_msg) ";
                $sql .= "VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message', '1', '1')";

                if (!$result = sql_query($sql)) {
                    Error::code('0020');
                }
            }

            global $subscribe, $nuke_url, $sitename;
            if ($subscribe) {
                $sujet = html_entity_decode(translate_ml($user_languex, "Notification message privé."), ENT_COMPAT | ENT_HTML401, cur_charset) . '[' . $from_userid . '] / ' . $sitename;
                $message = $time . '<br />' . translate_ml($user_languex, "Bonjour") . '<br />' . translate_ml($user_languex, "Vous avez un nouveau message.") . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="' . $nuke_url . '/viewpmsg.php">' . translate_ml($user_languex, "Cliquez ici pour lire votre nouveau message.") . '</a><br />';
                
                include("signat.php");

                Mailer::copy_to_email($to_useridx, $sujet, stripslashes($message));
            }
        }
    }
 
    /**
     * Formulaire d'écriture d'un MI
     *
     * @param   [type]  $to_userid  [$to_userid description]
     *
     * @return  [type]              [return description]
     */    
    public static function write_short_private_message($to_userid)
    {
        echo '
        <h2>' . translate("Message à un membre") . '</h2>
        <h3><i class="fa fa-at me-1"></i>' . $to_userid . '</h3>
        <form id="sh_priv_mess" action="powerpack.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject" >' . translate("Sujet") . '</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="subject" name="subject" maxlength="100" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="message" >' . translate("Message") . '</label>
                <div class="col-sm-12">
                    <textarea class="form-control"  id="message" name="message" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <div class="form-check" >
                    <input class="form-check-input" type="checkbox" id="copie" name="copie" />
                    <label class="form-check-label" for="copie">' . translate("Conserver une copie") . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <input type="hidden" name="to_userid" value="' . $to_userid . '" />
                <input type="hidden" name="op" value="write_instant_message" />
                <div class="col-sm-12">
                    <input class="btn btn-primary" type="submit" name="submit" value="' . translate("Valider") . '" accesskey="s" />&nbsp;
                    <button class="btn btn-secondary" type="reset">' . translate("Annuler") . '</button>
                </div>
            </div>
        </form>';
    }

    /**
     * Ouvre la page d'envoi d'un MI (Message Interne)
     *
     * @param   [type]  $to_userid  [$to_userid description]
     *
     * @return  [type]              [return description]
     */
    public static function Form_instant_message($to_userid)
    {
        include("header.php");
        static::write_short_private_message(Hack::removeHack($to_userid));
        include("footer.php");
    }
    
}