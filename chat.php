<?php

use Npds\Config\Config;
use Npds\Support\Facades\Css;
use Npds\Support\Facades\Str;
use Npds\Support\Facades\Chat;
use Npds\Support\Facades\Hack;
use Npds\Support\Facades\Crypt;
use Npds\Support\Facades\Theme;
use Npds\Support\Facades\Groupe;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Smilies;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

/**
 * [chat description]
 *
 * @return  [type]  [return description]
 */
function chat()
{
    $Titlesitename = 'NPDS';
    $meta_op = '';
    $meta_doctype = '<!DOCTYPE html>';

    include("storage/meta/meta.php");

    $id     = Request::query('id');
    $auto   = Request::query('auto');

    echo '
                <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />
            </head>  
                <div style="height:1vh;" class=""><iframe src="'. site_url('chat.php?op=chatrafraich&repere=0&aff_entetes=1&connectes=-1&id=' . $id . '&auto=' . $auto) . '" frameborder="0" scrolling="no" noresize="noresize" name="rafraich" width="100%" height="100%"></iframe></div>
                <div style="height:58vh;" class=""><iframe src="'. site_url('chat.php?op=chattop') .'" frameborder="0" scrolling="yes" noresize="noresize" name="haut" width="100%" height="100%"></iframe></div>
                <div style="height:39vh;" class=""><iframe src="'. site_url('chat.php?op=chatinput&id=' . $id . '&amp;auto=' . $auto) . '" frameborder="0" scrolling="yes" noresize="noresize" name="bas" width="100%" height="100%"></iframe></div>
        </html>';
}

/**
 * [chatrafraich description]
 *
 * @return  [type]  [return description]
 */
function chatrafraich()
{
    global $Default_Theme, $Default_Skin, $user, $language, $admin, $cookie;

    $id             = Request::query('id');
    $auto           = Request::query('auto');
    $repere         = Request::query('repere');
    $aff_entetes    = Request::query('aff_entetes');
    $connectes      = Request::query('connectes');
    
    // chatbox avec salon privatif - on utilise id pour filtrer les messages -> id = l'id du groupe au sens autorisation de NPDS (-127,-1,0,1,2...126)
    if ($id === '' || unserialize(Crypt::decrypt($auto)) != $id) {
        die();
    }

    // Savoir si le 'connecté' a le droit à ce chat ?
    if (!Groupe::autorisation($id)) {
        die();
    }

    if (isset($user) and $user != '') {
        if ($cookie[9] != '') {
            $ibix = explode('+', urldecode($cookie[9]));

            if (array_key_exists(0, $ibix)) {
                $theme = $ibix[0];
            } else {
                $theme = $Default_Theme;}

            if (array_key_exists(1, $ibix)) {
                $skin = $ibix[1];
            } else {
                $skin = $Default_Skin; 
            }

            $tmp_theme = $theme;

            if (!$file = @opendir("Themes/$theme")) {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $tmp_theme = $Default_Theme;
        }
    } else {
        $theme = $Default_Theme;
        $skin = $Default_Skin;
        $tmp_theme = $theme;
    }

    $result = sql_query("SELECT username, message, dbname, date 
                         FROM " . sql_table('chatbox') . " 
                         WHERE id='$id' 
                         AND date>'$repere' 
                         ORDER BY date ASC");

    $thing = '';

    if ($result) {

        while (list($username, $message, $dbname, $date_message) = sql_fetch_row($result)) {
            $thing .= "<div class='chatmessage'>
                <div class='chatheure'>
                    " . date(translate("Chatdate"), $date_message + ((int) Config::get('date.gmt') * 3600)) . "
                </div>";
            
            if ($dbname == 1) {
                if ((!$user) and (Config::get('user.member_list') == 1) and (!$admin)) {
                    $thing .= "<div class='chatnom'>$username</div>";
                } else {
                    $thing .= "<div class='chatnom'>
                            <div class='float-start'>
                                " . str_replace('"', '\"', Theme::userpopover($username, 36, 1)) . "
                            </div> 
                            <a href='". site_url('user.php?op=userinfo&amp;uname='. $username) ."' target='_blank'>
                                $username
                            </a>
                        </div>";
                }
            } else {
                $thing .= "<div class='chatnom'>$username</div>";
            }

            $message = Smilies::smilie($message);

            $chat_forbidden_words = array(
                "'\"'i" => '&quot;',
                "'OxOA'i" => '',
                "'OxOD'i" => '',
                "'\n'i" => '',
                "'\r'i" => '',
                "'\t'i" => ''
            );
            
            $message = preg_replace(array_keys($chat_forbidden_words), array_values($chat_forbidden_words), $message);
            $message = str_replace('"', '\"', Str::make_clickable($message));

            $thing .= "<div class='chattexte'>" . Hack::removeHack($message) . "</div></div>";
            $repere = $date_message;
        }

        $thing = "\"" . $thing . "\"";
    }

    if ($aff_entetes == '1') {
        $meta_op = true;
        
        // settype($Xthing, 'string');

        include("storage/meta/meta.php");

        $Xthing .= $l_meta;
        $Xthing .= str_replace("\n", "", Css::importCssJavascript($tmp_theme, $language, $skin, strtolower(basename($_SERVER['PHP_SELF'])), ""));
        $Xthing .= "</head><body id='chat'>";
        $Xthing = "\"" . str_replace("'", "\'", $Xthing) . "\"";
    }

    $result = sql_query("SELECT DISTINCT ip 
                         FROM " . sql_table('chatbox') . " 
                         WHERE id='$id' AND 
                         date >= " . (time() - (60 * 2)) . "");
                         
    $numofchatters = sql_num_rows($result);

    $rafraich_connectes = 0;

    if (intval($connectes) != $numofchatters) {
        $rafraich_connectes = 1;
        
        if (($numofchatters == 1) or ($numofchatters == 0)) {
            $nbre_connectes = "'" . $numofchatters . " " . Str::utf8_java(html_entity_decode(translate("personne connectée."), ENT_QUOTES | ENT_HTML401, 'UTF-8')) . " GP [$id]'";
        } else {
            $nbre_connectes = "'" . $numofchatters . " " . Str::utf8_java(html_entity_decode(translate("personnes connectées."), ENT_QUOTES | ENT_HTML401, 'UTF-8')) . " GP [$id]'";
        }
    }

    $commande = "self.location='". site_url('chat.php?op=chatrafraich&repere='. $repere .'&aff_entetes=0&connectes='. $numofchatters .'&id='. $id .'&auto='. $auto) ."'";

    include("storage/meta/meta.php");

    echo "</head>\n<body id='chat'>
    <script type='text/javascript'>
    //<![CDATA[
    function scroll_messages() {
        if (typeof(scrollBy) != 'undefined') {
            parent.frames[1].scrollBy(0, 20000);
            parent.frames[1].scrollBy(0, 20000);
        }
        else if (typeof(scroll) != 'undefined') {
            parent.frames[1].scroll(0, 20000);
            parent.frames[1].scroll(0, 20000);
        }
    }

    function rafraichir() {
        $commande;
    }

    function sur_chargement() {
        setTimeout(\"rafraichir();\", 5000);";

    if ($aff_entetes == "1") {
        echo "parent.frames[1].document.write($Xthing);";
    }

    if ($thing != "\"\"") {
        echo "parent.frames[1].document.write($thing);
                setTimeout(\"scroll_messages();\", 300);
                ";
    }

    if ($rafraich_connectes == 1) {
        echo "top.document.title=$nbre_connectes;";
    }

    echo "}
    window.onload=sur_chargement();
    //]]>
    </script>
    </body>
    </html>";
}

/**
 * [chattop description]
 *
 * @return  [type]  [return description]
 */
function chattop()
{
    $Titlesitename = 'NPDS';
    $nuke_url = '';
    $meta_op = '';
    
    include("Bootstrap/Boot.php");
    include('storage/meta/meta.php');

    echo '
       </head>
       <body>
       </body>
    </html>';
}

/**
 * [chatinput description]
 *
 * @return  [type]  [return description]
 */
function chatinput()
{
    global $Default_Theme, $Default_Skin, $user, $cookie, $language;

    // chatbox avec salon privatif - on utilise id pour filtrer les messages -> id = l'id du groupe au sens autorisation de NPDS (-127,-1,0,1,2...126))
    $id     = Request::input('id');
    $auto   = Request::input('auto');

    if ($id === '' || unserialize(Crypt::decrypt($auto)) != $id) {
        die();
    }

    // Savoir si le 'connecté' a le droit à ce chat ?
    // le problème c'est que tous les groupes qui existent on le droit au chat ... donc il faut trouver une solution pour pouvoir l'interdire
    // soit on vient d'un bloc qui par définition autorise en fabricant l'interface
    // soit on viens de WS et là ....

    if (!Groupe::autorisation($id)) {
        die();
    }

    if (isset($user) and $user != '') {

        if ($cookie[9] != '') {
            $ibix = explode('+', urldecode($cookie[9]));

            if (array_key_exists(0, $ibix)) {
                $theme = $ibix[0];
            } else {
                $theme = $Default_Theme;
            }

            if (array_key_exists(1, $ibix)) {
                $skin = $ibix[1];
            } else {
                $skin = $Default_Skin;
            }

            $tmp_theme = $theme;

            if (!$file = @opendir("Themes/$theme")) {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $tmp_theme = $Default_Theme;
        }
    } else {
        $theme = $Default_Theme;
        $skin = $Default_Skin;
        $tmp_theme = $theme;
    }

    $skin = $skin == '' ? 'default' : $skin;

    $Titlesitename = 'NPDS';

    include("storage/meta/meta.php");

    echo Css::importCss($tmp_theme, $language, $skin, strtolower(basename($_SERVER['PHP_SELF'])), '');

    include("assets/formhelp.java.php");

    echo '</head>';

    // cookie chat_info (1 par groupe)
    echo '<script type="text/javascript" src="assets/js/cookies.js"></script>';
    echo "<body id=\"chat\" onload=\"setCookie('chat_info_$id', '1', '');\" onUnload=\"deleteCookie('chat_info_$id');\">";
    echo '<script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="assets/shared/font-awesome/css/all.min.css">
        <form name="coolsus" action="'. site_url('chat.php') .'" method="post">
        <input type="hidden" name="op" value="chatinput" />
        <input type="hidden" name="subop" value="set" />
        <input type="hidden" name="id" value="' . $id . '" />
        <input type="hidden" name="auto" value="' . $auto . '" />';

    if (!isset($cookie[1])) {
        $pseudo = ((isset($name)) ? ($name) : urldecode(Request::getip()));
    } else {
        $pseudo = $cookie[1];
    }

    $xJava = 'name="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="storeForm(this)"';

    echo translate("Vous êtes connecté en tant que :") . ' <strong>' . $pseudo . '</strong>&nbsp;';

    echo '
        <input type="hidden" name="name" value="' . $pseudo . '" />
        <textarea id="chatarea" class="form-control my-3" type="text" rows="2" ' . $xJava . ' placeholder="🖋"></textarea>
        <div class="float-end">';

    Smilies::putitems("chatarea");
    
    echo '
            </div>
            <input class="btn btn-primary btn-sm" type="submit" tabindex="1" value="' . translate("Valider") . '" />
            </form>
            <script src="assets/js/npds_adapt.js"></script>
            <script type="text/javascript">
            //<![CDATA[
                document.coolsus.message.focus();
            //]]>
            </script>
        </body>
    </html>';

    switch (Request::input('subop')) {

        case 'set':
            Chat::insertMessageChat();
            break;
    }
}

switch (Request::input('op')) {

    case 'chatrafraich':
        chatrafraich();
        break;

    case 'chattop':
        chattop();
        break;

    case 'chatinput':
        chatinput();
        break;

    case 'admin_chatbox_write':
        Chat::chatboxWrite();
        break;

    default:
        chat();
        break;
}
