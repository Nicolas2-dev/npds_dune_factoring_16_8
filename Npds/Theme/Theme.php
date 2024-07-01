<?php

namespace Npds\Theme;

use Npds\Support\Facades\Str;
use Npds\Support\Facades\Date;
use Npds\Support\Facades\Spam;
use Npds\Support\Facades\User;
use Npds\Support\Facades\Groupe;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Metalang;
use Npds\Contracts\Theme\ThemeInterface;


/**
 * Theme class
 */
class Theme implements ThemeInterface
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
     * [lists description]
     *
     * @return  [type]  [return description]
     */
    public static function lists()
    {
        $handle = opendir('Themes');

        while (false !== ($file = readdir($handle))) {
            if (($file[0] !== '_') 
            and (!strstr($file, '.')) 
            and (!strstr($file, 'default'))) {
                $themelist[] = $file;
            }
        }

        natcasesort($themelist);
        $themelist = implode(' ', $themelist);
        closedir($handle);

        return $themelist;
    }

    /**
     * [local_var description]
     *
     * @param   [type]  $Xcontent  [$Xcontent description]
     *
     * @return  [type]             [return description]
     */
    public static function local_var($Xcontent)
    {
        if (strstr($Xcontent, "!var!")) {
            $deb = strpos($Xcontent, "!var!", 0) + 5;
            $fin = strpos($Xcontent, ' ', $deb);

            if ($fin) {
                $H_var = substr($Xcontent, $deb, $fin - $deb);
            } else {
                $H_var = substr($Xcontent, $deb);
            }

            return $H_var;
        }
    }
    
    /**
     * [colsyst description]
     *
     * @param   [type]  $coltarget  [$coltarget description]
     *
     * @return  [type]              [return description]
     */
    public static function colsyst($coltarget)
    {
        $coltoggle = '
        <div class="col d-lg-none me-2 my-2">
            <hr />
            <a class=" small float-end" href="#" data-bs-toggle="collapse" data-bs-target="' . $coltarget . '"><span class="plusdecontenu trn">Plus de contenu</span></a>
        </div>';
        
        echo $coltoggle;
    }

    /**
     * [themeindex description]
     *
     * @param   [type]  $aid         [$aid description]
     * @param   [type]  $informant   [$informant description]
     * @param   [type]  $time        [$time description]
     * @param   [type]  $title       [$title description]
     * @param   [type]  $counter     [$counter description]
     * @param   [type]  $topic       [$topic description]
     * @param   [type]  $thetext     [$thetext description]
     * @param   [type]  $notes       [$notes description]
     * @param   [type]  $morelink    [$morelink description]
     * @param   [type]  $topicname   [$topicname description]
     * @param   [type]  $topicimage  [$topicimage description]
     * @param   [type]  $topictext   [$topictext description]
     * @param   [type]  $id          [$id description]
     *
     * @return  [type]               [return description]
     */
    public static function themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id)
    {
        global $tipath, $theme, $nuke_url;

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/html/index-news.html")) {
            $inclusion = "themes/" . $theme . "/html/index-news.html";
        } elseif (file_exists("themes/default/html/index-news.html")) {
            $inclusion = "themes/default/html/index-news.html";
        } else {
            echo 'index-news.html manquant / not find !<br />';
            die();
        }

        $H_var = static::local_var($thetext);

        if ($H_var != '') {
            ${$H_var} = true;
            $thetext = str_replace("!var!$H_var", "", $thetext);
        }

        if ($notes != '') {
            $notes = '<div class="note">' . translate("Note") . ' : ' . $notes . '</div>';
        }

        ob_start();
            include($inclusion);
            $Xcontent = ob_get_contents();
        ob_end_clean();
    
        $lire_la_suite = '';
        if ($morelink[0]) {
            $lire_la_suite = $morelink[1] . ' ' . $morelink[0] . ' | ';
        }

        $commentaire = '';

        if ($morelink[2]) {
            $commentaire = $morelink[2] . ' ' . $morelink[3] . ' | ';
        } else {
            $commentaire = $morelink[3] . ' | ';
        }

        $categorie = '';

        if ($morelink[6]) {
            $categorie = ' : ' . $morelink[6];
        }

        $morel = $lire_la_suite . $commentaire . $morelink[4] . ' ' . $morelink[5] . $categorie;
    
        $Xsujet = '';

        if ($topicimage != '') {
            if (!$imgtmp = static::theme_image('topics/' . $topicimage)) {
                
            }

            $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $imgtmp . '" alt="' . translate("Rechercher dans") . ' : ' . $topicname . '" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true" /></a>';
        } else {
            $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><span class="badge bg-secondary h1" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true">' . $topicname . '</span></a>';
        }
        
        $npds_METALANG_words = array(
            "'!N_publicateur!'i" => $aid,
            "'!N_emetteur!'i" => static::userpopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '">' . $informant . '</a>',
            "'!N_date!'i" => Date::formatTimestamp($time),
            // "'!N_date_y!'i"=>substr($time,0,4),
            // "'!N_date_m!'i"=>formatTimestamp($time), //strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
            // "'!N_date_d!'i"=>substr($time,8,2),
            // "'!N_date_h!'i"=>substr($time,11),
            "'!N_print!'i" => $morelink[4],
            "'!N_friend!'i" => $morelink[5],
            "'!N_nb_carac!'i" => $morelink[0],
            "'!N_read_more!'i" => $morelink[1],
            "'!N_nb_comment!'i" => $morelink[2],
            "'!N_link_comment!'i" => $morelink[3],
            "'!N_categorie!'i" => $morelink[6],
            "'!N_titre!'i" => $title,
            "'!N_texte!'i" => $thetext,
            "'!N_id!'i" => $id,
            "'!N_sujet!'i" => $Xsujet,
            "'!N_note!'i" => $notes,
            "'!N_nb_lecture!'i" => $counter,
            "'!N_suite!'i" => $morel
        );

        echo Metalang::meta_lang(Language::aff_langue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
    }
    
    /**
     * [themearticle description]
     *
     * @param   [type]  $aid           [$aid description]
     * @param   [type]  $informant     [$informant description]
     * @param   [type]  $time          [$time description]
     * @param   [type]  $title         [$title description]
     * @param   [type]  $thetext       [$thetext description]
     * @param   [type]  $topic         [$topic description]
     * @param   [type]  $topicname     [$topicname description]
     * @param   [type]  $topicimage    [$topicimage description]
     * @param   [type]  $topictext     [$topictext description]
     * @param   [type]  $id            [$id description]
     * @param   [type]  $previous_sid  [$previous_sid description]
     * @param   [type]  $next_sid      [$next_sid description]
     * @param   [type]  $archive       [$archive description]
     *
     * @return  [type]                 [return description]
     */
    public static function themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive)
    {
        global $tipath, $theme, $nuke_url, $counter;
        global $boxtitle, $boxstuff, $short_user, $user;

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/html/detail-news.html")) {
            $inclusion = "themes/" . $theme . "/html/detail-news.html";
        } elseif (file_exists("themes/default/html/detail-news.html")) {
            $inclusion = "themes/default/html/detail-news.html";
        } else {
            echo 'detail-news.html manquant / not find !<br />';
            die();
        }

        $H_var = static::local_var($thetext);

        if ($H_var != '') {
            ${$H_var} = true;
            $thetext = str_replace("!var!$H_var", '', $thetext);
        }

        ob_start();
            include($inclusion);
            $Xcontent = ob_get_contents();
        ob_end_clean();

        if ($previous_sid) {
            $prevArt = '<a href="article.php?sid=' . $previous_sid . '&amp;archive=' . $archive . '" ><i class="fa fa-chevron-left fa-lg me-2" title="' . translate("Précédent") . '" data-bs-toggle="tooltip"></i><span class="d-none d-sm-inline">' . translate("Précédent") . '</span></a>';
        } else {
            $prevArt = '';
        }

        if ($next_sid) {
            $nextArt = '<a href="article.php?sid=' . $next_sid . '&amp;archive=' . $archive . '" ><span class="d-none d-sm-inline">' . translate("Suivant") . '</span><i class="fa fa-chevron-right fa-lg ms-2" title="' . translate("Suivant") . '" data-bs-toggle="tooltip"></i></a>';
        } else {
            $nextArt = '';
        }
    
        $printP = '<a href="print.php?sid=' . $id . '" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-print"></i></a>';
        $sendF = '<a href="friend.php?op=FriendSend&amp;sid=' . $id . '" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-at"></i></a>';
    
        if (!$imgtmp = static::theme_image('topics/' . $topicimage)) {
            $imgtmp = $tipath . $topicimage;
        }

        $timage = $imgtmp;
    
        $npds_METALANG_words = array(
            "'!N_publicateur!'i" => $aid,
            "'!N_emetteur!'i" => static::userpopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '"><span class="">' . $informant . '</span></a>',
            "'!N_date!'i" => Date::formatTimestamp($time),
            // "'!N_date_y!'i"=>substr($time,0,4),
            // "'!N_date_m!'i"=>formatTimestamp($time), //strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
            // "'!N_date_d!'i"=>substr($time,8,2),
            // "'!N_date_h!'i"=>substr($time,11),
            "'!N_print!'i" => $printP,
            "'!N_friend!'i" => $sendF,
            "'!N_boxrel_title!'i" => $boxtitle,
            "'!N_boxrel_stuff!'i" => $boxstuff,
            "'!N_titre!'i" => $title,
            "'!N_id!'i" => $id,
            "'!N_previous_article!'i" => $prevArt,
            "'!N_next_article!'i" => $nextArt,
            "'!N_sujet!'i" => '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $timage . '" alt="' . translate("Rechercher dans") . '&nbsp;' . $topictext . '" /></a>',
            "'!N_texte!'i" => $thetext,
            "'!N_nb_lecture!'i" => $counter
        );

        echo Metalang::meta_lang(Language::aff_langue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
    }
    
    /**
     * [themesidebox description]
     *
     * @param   [type]  $title    [$title description]
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public static function themesidebox($title, $content)
    {
        global $theme, $B_class_title, $B_class_content, $bloc_side, $htvar;

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/html/bloc-right.html") and ($bloc_side == "RIGHT")) {
            $inclusion = 'themes/' . $theme . '/html/bloc-right.html';
        }

        if (file_exists("themes/" . $theme . "/html/bloc-left.html") and ($bloc_side == "LEFT")) {
            $inclusion = 'themes/' . $theme . '/html/bloc-left.html';
        }

        if (!$inclusion) {
            if (file_exists("themes/" . $theme . "/html/bloc.html")) {
                $inclusion = 'themes/' . $theme . '/html/bloc.html';
            } elseif (file_exists("themes/default/html/bloc.html")) {
                $inclusion = 'themes/default/html/bloc.html';
            } else {
                echo 'bloc.html manquant / not find !<br />';
                die();
            }
        }

        ob_start();
            include($inclusion);
            $Xcontent = ob_get_contents();
        ob_end_clean();

        if ($title == 'no-title') {
            $Xcontent = str_replace('<div class="LB_title">!B_title!</div>', '', $Xcontent);
            $title = '';
        }

        $npds_METALANG_words = array(
            "'!B_title!'i" => $title,
            "'!B_class_title!'i" => $B_class_title,
            "'!B_class_content!'i" => $B_class_content,
            "'!B_content!'i" => $content
        );

        echo $htvar; 
        echo Metalang::meta_lang(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent));
        echo '</div>'; 
    }
    
    /**
     * [themedito description]
     *
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public static function themedito($content)
    {
        global $theme;

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/html/editorial.html")) {
            $inclusion = "themes/" . $theme . "/html/editorial.html";
        } elseif (file_exists("themes/default/html/editorial.html")) {
            $inclusion = "themes/default/html/editorial.html";
        } else {
            echo 'editorial.html manquant / not find !<br />';
            die();
        }

        if ($inclusion) {
            ob_start();
                include($inclusion);
                $Xcontent = ob_get_contents();
            ob_end_clean();

            $npds_METALANG_words = array(
                "'!editorial_content!'i" => $content
            );

            echo Metalang::meta_lang(Language::aff_langue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
        }

        return $inclusion;
    }

    /**
     * à partir du nom de l'utilisateur ($who) $avpop à 1 : affiche son avatar (ou avatar defaut) au dimension ($dim qui défini la class n-ava-$dim)
     * $avpop à 2 : l'avatar affiché commande un popover contenant diverses info de cet utilisateur et liens associés
     * 
     * @param   [type]  $who    [$who description]
     * @param   [type]  $dim    [$dim description]
     * @param   [type]  $avpop  [$avpop description]
     *
     * @return  [type]          [return description]
     */
    public static function userpopover($who, $dim, $avpop)
    {
        global $short_user, $user;

        $result = sql_query("SELECT uname 
                             FROM " . sql_table('users') . " 
                             WHERE uname ='$who'");

        if (sql_num_rows($result)) {

            $temp_user = User::getUserData($who);

            $socialnetworks = array();
            $posterdata_extend = array();
            $res_id = array();

            $my_rs = '';

            if (!$short_user) {
                if ($temp_user['uid'] != 1) {

                    $posterdata_extend = User::getUserDataExtendFromId($temp_user['uid']);

                    include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');
                    include('modules/geoloc/geoloc.conf');

                    if ($user or Groupe::autorisation(-127)) {
                        if ($posterdata_extend['M2'] != '') {

                            $socialnetworks = explode(';', $posterdata_extend['M2']);

                            foreach ($socialnetworks as $socialnetwork) {
                                $res_id[] = explode('|', $socialnetwork);
                            }

                            sort($res_id);
                            sort($rs);

                            foreach ($rs as $v1) {
                                foreach ($res_id as $y1) {
                                    $k = array_search($y1[0], $v1);

                                    if (false !== $k) {
                                        $my_rs .= '<a class="me-2 " href="';

                                        if ($v1[2] == 'skype') {
                                            $my_rs .= $v1[1] . $y1[1] . '?chat';
                                        } else {
                                            $my_rs .= $v1[1] . $y1[1];
                                        }

                                        $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                        break;
                                    } else {
                                        $my_rs .= '';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            settype($ch_lat, 'string');

            $useroutils = '';

            if ($user or Groupe::autorisation(-127)) {
                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="user.php?op=userinfo&amp;uname=' . $temp_user['uname'] . '" target="_blank" title="' . translate("Profil") . '" ><i class="fa fa-lg fa-user align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Profil") . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="powerpack.php?op=instant_message&amp;to_userid=' . urlencode($temp_user['uname']) . '" title="' . translate("Envoyer un message interne") . '" ><i class="far fa-lg fa-envelope align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Message") . '</span></a></li>';
                }

                if ($temp_user['femail'] != '') {
                    $useroutils .= '<li><a class="dropdown-item  text-center text-md-start" href="mailto:' . Spam::anti_spam($temp_user['femail'], 1) . '" target="_blank" title="' . translate("Email") . '" ><i class="fa fa-at fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Email") . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and array_key_exists($ch_lat, $posterdata_extend)) {
                    if ($posterdata_extend[$ch_lat] != '') {
                        $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u' . $temp_user['uid'] . '" title="' . translate("Localisation") . '" ><i class="fas fa-map-marker-alt fa-lg align-middle fa-fw">&nbsp;</i><span class="ms-2 d-none d-md-inline">' . translate("Localisation") . '</span></a></li>';
                    }
                }
            }

            if ($temp_user['url'] != '') {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="' . $temp_user['url'] . '" target="_blank" title="' . translate("Visiter ce site web") . '"><i class="fas fa-external-link-alt fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visiter ce site web") . '</span></a></li>';
            }

            if ($temp_user['mns']) {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="minisite.php?op=' . $temp_user['uname'] . '" target="_blank" target="_blank" title="' . translate("Visitez le minisite") . '" ><i class="fa fa-lg fa-desktop align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visitez le minisite") . '</span></a></li>';
            }

            if (stristr($temp_user['user_avatar'], 'users_private')) {
                $imgtmp = $temp_user['user_avatar'];
            } else {
                if ($ibid = static::theme_image('forum/avatar/' . $temp_user['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = 'assets/images/forum/avatar/' . $temp_user['user_avatar'];
                }
            }
    
            $userpop = $avpop == 1 
                ? '<img class="btn-outline-primary img-thumbnail img-fluid n-ava-' . $dim . ' me-2" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />' 
                  // '<a tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="'.$temp_user['uname'].'" data-bs-content=\'<div class="list-group mb-3 text-center">'.$useroutils.'</div><div class="mx-auto text-center" style="max-width:170px;">'.$my_rs.'</div>\'></i><img data-bs-html="true" class="btn-outline-primary img-thumbnail img-fluid n-ava-'.$dim.' me-2" src="'.$imgtmp.'" alt="'.$temp_user['uname'].'" loading="lazy" /></a>' ;
    
                : '<div class="dropdown d-inline-block me-4 dropend">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <img class=" btn-outline-primary img-fluid n-ava-' . $dim . ' me-0" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" />
                    </a>
                    <ul class="dropdown-menu bg-light">
                        <li><span class="dropdown-item-text text-center py-0 my-0">' . static::userpopover($who, 64, 1) . '</span></li>
                        <li><h6 class="dropdown-header text-center py-0 my-0">' . $who . '</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        ' . $useroutils . '
                        <li><hr class="dropdown-divider"></li>
                        <li><div class="mx-auto text-center" style="max-width:170px;">' . $my_rs . '</div>
                    </ul>
                </div>';
    
            return $userpop;
        }
    }

    /**
     * Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
     *
     * @param   [type]  $theme_img  [$theme_img description]
     *
     * @return  [type]              [return description]
     */
    public static function theme_image($theme_img)
    {
        global $theme;

        if (@file_exists("themes/$theme/assets/images/$theme_img")) {
            return ("themes/$theme/assets/images/$theme_img");
        }
        
        return false;
    }

    /**
     * [image description]
     *
     * @param   [type]  $theme_img    [$theme_img description]
     * @param   [type]  $default_img  [$default_img description]
     *
     * @return  [type]                [return description]
     */
    public static function image($theme_img, $default_img = '')
    {
        global $theme;

        if (@file_exists("Themes/$theme/assets/images/$theme_img")) {
            return ("Themes/$theme/assets/images/$theme_img");
        } elseif (@file_exists("assets/images/$default_img")) {
            return ("assets/images/$default_img");
        }
        
        return false;
    }

    /**
     * [footmsg description]
     *
     * @return  [type]  [return description]
     */
    public static function footmsg()
    {
        global $foot1, $foot2, $foot3, $foot4;
    
        $foot = '<p align="center">';

        if ($foot1) {
            $foot .= stripslashes($foot1) . '<br />';
        }

        if ($foot2) {
            $foot .= stripslashes($foot2) . '<br />';
        }

        if ($foot3) {
            $foot .= stripslashes($foot3) . '<br />';
        }
        
        if ($foot4) {
            $foot .= stripslashes($foot4);
        }

        $foot .= '</p>';
    
        echo Language::aff_langue($foot);
    }
    
    /**
     * [foot description]
     *
     * @return  [type]  [return description]
     */
    public static function foot()
    {
        global $user, $Default_Theme, $cookie9;
    
        if ($user) {
            $user2 = base64_decode($user);
            $cookie = explode(':', $user2);
    
            if ($cookie[9] == '') {
                $cookie[9] = $Default_Theme;
            }
    
            $ibix = explode('+', urldecode($cookie[9]));
    
            if (!$file = @opendir("Themes/$ibix[0]")) {
                include("Themes/$Default_Theme/footer.php");
            } else {
                include("Themes/$ibix[0]/footer.php");
            }
        } else {
            include("Themes/$Default_Theme/footer.php");
        }
    
        if ($user) {
            $cookie9 = $ibix[0];
        }

        return $cookie9;
    }

    /**
     * [theme_distinct description]
     *
     * @return  [type]  [return description]
     */
    public static function theme_distinct()
    {
        $content =  '<h3 class="my-4">' . translate("Thème(s)") . '</h3>
        <table data-toggle="table" data-striped="true">
            <thead>
                <tr>
                    <th data-sortable="true" data-halign="center">' . translate("Thème(s)") . '</th>
                    <th data-halign="center" data-align="right">' . translate("Nombre d'utilisateurs par thème") . '</th>
                    <th data-halign="center">' . translate("Status") . '</th>
                </tr>
            </thead>
            <tbody>';

        $resultX = sql_query("SELECT DISTINCT(theme) 
                              FROM " . sql_table('users'));

        while (list($themelist) = sql_fetch_row($resultX)) {
            if ($themelist != '') {
                $ibix = explode('+', $themelist);
                $T_exist = is_dir("Themes/$ibix[0]") ? '' : '<span class="text-danger">' . translate("Ce fichier n'existe pas ...") . '</span>';

                global $Default_Theme;
                if ($themelist == $Default_Theme) {
                    $result = sql_query("SELECT uid 
                                         FROM " . sql_table('users') . " 
                                         WHERE theme='$themelist'");

                    $themeD1 = $result ? sql_num_rows($result) : 0;

                    $result = sql_query("SELECT uid 
                                         FROM " . sql_table('users') . " 
                                         WHERE theme=''");

                    $themeD2 = $result ? sql_num_rows($result) : 0;

                    $content .= '
                    <tr>
                    <td>' . $themelist . ' <b>(' . translate("par défaut") . ')</b></td>
                    <td><b>' . Str::wrh(($themeD1 + $themeD2)) . '</b></td>
                    <td>' . $T_exist . '</td>
                    </tr>';
                } else {
                    $result = sql_query("SELECT uid 
                                         FROM " . sql_table('users') . " 
                                         WHERE theme='$themelist'");
                                         
                    $themeU = $result ? sql_num_rows($result) : 0;

                    $content .= '
                    <tr>';
                    $content .= substr($ibix[0], -3) == "_sk" ? '
                    <td>' . $themelist . '</td>' : '
                    <td>' . $ibix[0] . '</td>';
                    $content .= '
                    <td><b>' . Str::wrh($themeU) . '</b></td>
                    <td>' . $T_exist . '</td>
                    </tr>';
                }
            }
        }

        $content .= '
            </tbody>
        </table>';

        return $content;
    }

}