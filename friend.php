<?php

use Npds\Support\Facades\Css;
use Npds\Support\Facades\Log;
use Npds\Support\Facades\Url;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Spam;
use Npds\Support\Facades\Mailer;
use Npds\Support\Facades\Language;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

/**
 * [FriendSend description]
 *
 * @param   [type]  $sid      [$sid description]
 * @param   [type]  $archive  [$archive description]
 *
 * @return  [type]            [return description]
 */
function FriendSend($sid, $archive)
{
    // settype($sid, "integer");
    // settype($archive, "integer");

    $result = sql_query("SELECT title, aid 
                         FROM " . sql_table('stories') . " 
                         WHERE sid='$sid'");

    list($title, $aid) = sql_fetch_row($result);

    if (!$aid) {
        header("Location: index.php");
    }

    include("header.php");

    echo '
    <div class="card card-body">
    <h2><i class="fa fa-at fa-lg text-body-secondary"></i>&nbsp;' . translate("Envoi de l'article à un ami") . '</h2>
    <hr />
    <p class="lead">' . translate("Vous allez envoyer cet article") . ' : <strong>' . Language::aff_langue($title) . '</strong></p>
    <form id="friendsendstory" action="friend.php" method="post">
        <input type="hidden" name="sid" value="' . $sid . '" />';

    global $user;

    $yn = '';
    $ye = '';

    if ($user) {
        global $cookie;

        $result = sql_query("SELECT name, email 
                             FROM " . sql_table('users') . " 
                             WHERE uname='$cookie[1]'");

        list($yn, $ye) = sql_fetch_row($result);
    }

    echo '
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="fname" name="fname" maxlength="100" required="required" />
            <label for="fname">' . translate("Nom du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="fmail" name="fmail" maxlength="254" required="required" />
            <label for="fmail">' . translate("Email du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="yname" name="yname" value="' . $yn . '" maxlength="100" required="required" />
            <label for="yname">' . translate("Votre nom") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="ymail" name="ymail" value="' . $ye . '" maxlength="254" required="required" />
            <label for="ymail">' . translate("Votre Email") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
        </div>';

    echo '' . Spam::Q_spambot();

    echo '
        <input type="hidden" name="archive" value="' . $archive . '" />
        <input type="hidden" name="op" value="SendStory" />
        <button type="submit" class="btn btn-primary" title="' . translate("Envoyer") . '"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate("Envoyer") . '</button>
    </form>';

    $arg1 = '
    var formulid = ["friendsendstory"];
    inpandfieldlen("yname",100);
    inpandfieldlen("ymail",254);
    inpandfieldlen("fname",100);
    inpandfieldlen("fmail",254);';

    Css::adminfoot('fv', '', $arg1, '');
}

/**
 * [SendStory description]
 *
 * @param   [type]  $sid           [$sid description]
 * @param   [type]  $yname         [$yname description]
 * @param   [type]  $ymail         [$ymail description]
 * @param   [type]  $fname         [$fname description]
 * @param   [type]  $fmail         [$fmail description]
 * @param   [type]  $archive       [$archive description]
 * @param   [type]  $asb_question  [$asb_question description]
 * @param   [type]  $asb_reponse   [$asb_reponse description]
 *
 * @return  [type]                 [return description]
 */
function SendStory($sid, $yname, $ymail, $fname, $fmail, $archive, $asb_question, $asb_reponse)
{
    global $user;

    if (!$user) {
        //anti_spambot
        if (!Spam::R_spambot($asb_question, $asb_reponse, '')) {
            
            Log::Ecr_Log('security', "Send-Story Anti-Spam : name=" . $yname . " / mail=" . $ymail, '');
            
            Url::redirect_url("index.php");
            die();
        }
    }

    global $sitename, $nuke_url;

    // settype($sid, 'integer');
    // settype($archive, 'integer');

    $result2 = sql_query("SELECT title, time, topic 
                          FROM " . sql_table('stories') . " 
                          WHERE sid='$sid'");

    list($title, $time, $topic) = sql_fetch_row($result2);

    $result3 = sql_query("SELECT topictext 
                          FROM " . sql_table('topics') . " 
                          WHERE topicid='$topic'");

    list($topictext) = sql_fetch_row($result3);

    $subject = html_entity_decode(translate("Article intéressant sur"), ENT_COMPAT | ENT_HTML401, cur_charset) . " $sitename";
    
    $fname = Hack::removeHack($fname);
    
    $message = translate("Bonjour") . " $fname :\n\n" . translate("Votre ami") . " $yname " . translate("a trouvé cet article intéressant et a souhaité vous l'envoyer.") . "\n\n" . Language::aff_langue($title) . "\n" . translate("Date :") . " $time\n" . translate("Sujet : ") . " " . Language::aff_langue($topictext) . "\n\n" . translate("L'article") . " : <a href=\"$nuke_url/article.php?sid=$sid&amp;archive=$archive\">$nuke_url/article.php?sid=$sid&amp;archive=$archive</a>\n\n";
    
    include("signat.php");

    $fmail      = Hack::removeHack($fmail);
    $subject    = Hack::removeHack($subject);
    $message    = Hack::removeHack($message);
    $yname      = Hack::removeHack($yname);
    $ymail      = Hack::removeHack($ymail);

    $stop = false;
    
    if ((!$fmail) || ($fmail == "") || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) {
        $stop = true;
    }

    if ((!$ymail) || ($ymail == "") || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
        $stop = true;
    }
    
    if (!$stop) {
        Mailer::send_email($fmail, $subject, $message, $ymail, false, 'html', '');
    } else {
        $title = '';
        $fname = '';
    }

    $title = urlencode(Language::aff_langue($title));
    $fname = urlencode($fname);

    Header("Location: friend.php?op=StorySent&title=$title&fname=$fname");
}

/**
 * [StorySent description]
 *
 * @param   [type]  $title  [$title description]
 * @param   [type]  $fname  [$fname description]
 *
 * @return  [type]          [return description]
 */
function StorySent($title, $fname)
{
    include("header.php");

    $title = urldecode($title);
    $fname = urldecode($fname);
    
    if ($fname == '') {
        echo '<div class="alert alert-danger">' . translate("Erreur : Email invalide") . '</div>';
    } else {
        echo '<div class="alert alert-success">' . translate("L'article") . ' <strong>' . stripslashes($title) . '</strong> ' . translate("a été envoyé à") . '&nbsp;' . $fname . '<br />' . translate("Merci") . '</div>';
    }

    include("footer.php");
}

/**
 * [RecommendSite description]
 *
 * @return  [type]  [return description]
 */
function RecommendSite()
{
    global $user;

    if ($user) {
        global $cookie;

        $result = sql_query("SELECT name, email 
                             FROM " . sql_table('users') . " 
                             WHERE uname='$cookie[1]'");

        list($yn, $ye) = sql_fetch_row($result);
    } else {
        $yn = '';
        $ye = '';
    }

    include("header.php");

    echo '
    <div class="card card-body">
    <h2>' . translate("Recommander ce site à un ami") . '</h2>
    <hr />
    <form id="friendrecomsite" action="friend.php" method="post">
        <input type="hidden" name="op" value="SendSite" />
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="yname" name="yname" value="' . $yn . '" required="required" maxlength="100" />
            <label for="yname">' . translate("Votre nom") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="ymail" name="ymail" value="' . $ye . '" required="required" maxlength="100" />
            <label for="ymail">' . translate("Votre Email") . '</label>
        </div>
        <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="fname" name="fname" required="required" maxlength="100" />
            <label for="fname">' . translate("Nom du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="fmail" name="fmail" required="required" maxlength="100" />
            <label for="fmail">' . translate("Email du destinataire") . '</label>
            <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
        </div>
        ' . Spam::Q_spambot() . '
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <button type="submit" class="btn btn-primary"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate("Envoyer") . '</button>
            </div>
        </div>
    </form>';

    $arg1 = '
    var formulid = ["friendrecomsite"];
    inpandfieldlen("yname",100);
    inpandfieldlen("ymail",100);
    inpandfieldlen("fname",100);
    inpandfieldlen("fmail",100);';

    Css::adminfoot('fv', '', $arg1, '');
}

/**
 * [SendSite description]
 *
 * @param   [type]  $yname         [$yname description]
 * @param   [type]  $ymail         [$ymail description]
 * @param   [type]  $fname         [$fname description]
 * @param   [type]  $fmail         [$fmail description]
 * @param   [type]  $asb_question  [$asb_question description]
 * @param   [type]  $asb_reponse   [$asb_reponse description]
 *
 * @return  [type]                 [return description]
 */
function SendSite($yname, $ymail, $fname, $fmail, $asb_question, $asb_reponse)
{
    global $user;

    if (!$user) {
        //anti_spambot
        if (!Spam::R_spambot($asb_question, $asb_reponse, '')) {
            
            Log::Ecr_Log('security', "Friend Anti-Spam : name=" . $yname . " / mail=" . $ymail, '');
            
            Url::redirect_url("index.php");
            die();
        }
    }

    global $sitename, $nuke_url;
    $subject = html_entity_decode(translate("Site à découvrir : "), ENT_COMPAT | ENT_HTML401, cur_charset) . " $sitename";
    
    $fname = Hack::removeHack($fname);
    
    $message = translate("Bonjour") . " $fname :\n\n" . translate("Votre ami") . " $yname " . translate("a trouvé notre site") . " $sitename " . translate("intéressant et a voulu vous le faire connaître.") . "\n\n$sitename : <a href=\"$nuke_url\">$nuke_url</a>\n\n";
    
    include("signat.php");
    
    $fmail      = Hack::removeHack($fmail);
    $subject    = Hack::removeHack($subject);
    $message    = Hack::removeHack($message);
    $yname      = Hack::removeHack($yname);
    $ymail      = Hack::removeHack($ymail);

    $stop = false;
    
    if ((!$fmail) || ($fmail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) { 
        $stop = true;
    }
    
    if ((!$ymail) || ($ymail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
        $stop = true;
    }
    
    if (!$stop) {
        Mailer::send_email($fmail, $subject, $message, $ymail, false, 'html', '');
    } else {
        $fname = '';
    }

    Header("Location: friend.php?op=SiteSent&fname=$fname");
}

/**
 * [SiteSent description]
 *
 * @param   [type]  $fname  [$fname description]
 *
 * @return  [type]          [return description]
 */
function SiteSent($fname)
{
    include('header.php');

    if ($fname == '')
        echo '
            <div class="alert alert-danger lead" role="alert">
                <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
                ' . translate("Erreur : Email invalide") . '
            </div>';
    else
        echo '
            <div class="alert alert-success lead" role="alert">
                <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
                ' . translate("Nos références ont été envoyées à ") . ' ' . $fname . ', <br />
                <strong>' . translate("Merci de nous avoir recommandé") . '</strong>
            </div>';

    include('footer.php');
}

// settype($op, 'string');
// settype($archive, 'string');

switch ($op) {
    case 'FriendSend':
        FriendSend($sid, $archive);
        break;

    case 'SendStory':
        SendStory($sid, $yname, $ymail, $fname, $fmail, $archive, $asb_question, $asb_reponse);
        break;

    case 'StorySent':
        StorySent($title, $fname);
        break;

    case 'SendSite':
        SendSite($yname, $ymail, $fname, $fmail, $asb_question, $asb_reponse);
        break;

    case 'SiteSent':
        SiteSent($fname);
        break;

    default:
        RecommendSite();
        break;
}
