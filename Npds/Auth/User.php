<?php

namespace Npds\Auth;

use Npds\Support\Facades\Error;
use Npds\Support\Facades\Cookie;
use Npds\Support\Facades\Request;
use Npds\Contracts\Auth\UserInterface;
use Npds\Support\Facades\Protect as HttpProtect;


/**
 * User class
 */
class User implements UserInterface
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
     * [getUser description]
     *
     * @return  [type]  [return description]
     */
    public static function getUser()
    {
        $user = Request::cookie('user');

        if (isset($user)) {
            $ibid = explode(':', base64_decode($user));
            array_walk($ibid, [HttpProtect::class, 'url_protect']);
            $user = base64_encode(str_replace("%3A", ":", urlencode(base64_decode($user))));
        
            return $user;
        }

        return false;
    }

    /**
     * [userCookie description]
     *
     * @param   [type]  $user  [$user description]
     *
     * @return  [type]         [return description]
     */
    public static function userCookie($user)
    {
        $cookie = null;
        
        if (isset($user)) {
            $cookie = Cookie::decode($user); 
        }
        
        return $cookie;
    }

    /**
     * Renvoi le contenu de la table users pour le user uname
     *
     * @param   [type]  $user  [$user description]
     *
     * @return  [type]         [return description]
     */
    public static function getUsrInfo($user)
    {
        $cookie = explode(':', base64_decode($user));

        $result = sql_query("SELECT pass 
                             FROM " . sql_table('users') . " 
                             WHERE uname='$cookie[1]'");

        list($pass) = sql_fetch_row($result);

        $userinfo = '';
        if (($cookie[2] == md5($pass)) and ($pass != '')) {
            
            $result = sql_query("SELECT uid, name, uname, email, femail, url, user_avatar, user_occ, user_from, user_intrest, user_sig, user_viewemail, user_theme, pass, storynum, umode, uorder, thold, noscore, bio, ublockon, ublock, theme, commentmax, user_journal, send_email, is_visible, mns, user_lnl 
                                 FROM " . sql_table('users') . " 
                                 WHERE uname='$cookie[1]'");
            
            if (sql_num_rows($result) == 1) {
                $userinfo = sql_fetch_assoc($result);
            } else {
                echo '<strong>' . translate("Un problème est survenu") . '.</strong>';
            }
        }

        return $userinfo;
    }

    /**
     * Pour savoir si le visiteur est un : membre ou admin (static.php et banners.php par exemple)
     *
     * @param   [type]  $sec_type  [$sec_type description]
     *
     * @return  [type]             [return description]
     */
    public static function securStatic($sec_type)
    {
        global $user, $admin;

        switch ($sec_type) {

            case 'member':
                if (isset($user)) {
                    return true;
                } else {
                    return false;
                }
                break;

            case 'admin':
                if (isset($admin)) {
                    return true;
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * Si AutoRegUser = true et que le user ne dispose pas du droit de connexion : RAZ du cookie NPDS 
     * retourne False ou True
     *
     * @return  [type]  [return description]
     */
    public static function autoReg()
    {
        global $AutoRegUser, $user;

        if (!$AutoRegUser) {
            if (isset($user)) {
                $cookie = explode(':', base64_decode($user));

                list($test) = sql_fetch_row(sql_query("SELECT open 
                                                       FROM " . sql_table('users_status') . " 
                                                       WHERE uid='$cookie[0]'"));

                if (!$test) {
                    setcookie('user', '', 0);
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * [getModerator description]
     *
     * @param   [type]  $user_id  [$user_id description]
     *
     * @return  [type]            [return description]
     */
    public static function getModerator($user_id)
    {
        $user_id = str_replace(",", "' or uid='", $user_id);

        if ($user_id == 0) {
            return "None";
        }

        $rowQ1 = Q_Select("SELECT uname 
                           FROM " . sql_table('users') . " 
                           WHERE uid='$user_id'", 3600);
        $modslist = '';

        foreach ($rowQ1 as $modnames) {
            foreach ($modnames as $modname) {
                $modslist .= $modname . ' ';
            }
        }

        return chop($modslist);
    }
    
    /**
     * [userIsModerator description]
     *
     * @param   [type]  $uidX           [$uidX description]
     * @param   [type]  $passwordX      [$passwordX description]
     * @param   [type]  $forum_accessX  [$forum_accessX description]
     *
     * @return  [type]                  [return description]
     */
    public static function userIsModerator($uidX, $passwordX, $forum_accessX)
    {
        $result1 = sql_query("SELECT pass 
                              FROM " . sql_table('users') . " 
                              WHERE uid = '$uidX'");

        $userX = sql_fetch_assoc($result1);

        $password = $userX['pass'];

        $result2 = sql_query("SELECT level 
                              FROM " . sql_table('users_status') . " 
                              WHERE uid = '$uidX'");      

        $userX = sql_fetch_assoc($result2);

        if ((md5($password) == $passwordX) and ($forum_accessX <= $userX['level']) and ($userX['level'] > 1)) {
            return $userX['level'];
        } else {
            return false;
        }
    }
    
    /**
     * [getUserDataFromId description]
     *
     * @param   [type]  $userid  [$userid description]
     *
     * @return  [type]           [return description]
     */
    public static function getUserDataFromId($userid)
    {
        $sql1 = "SELECT * 
                 FROM " . sql_table('users') . " 
                 WHERE uid='$userid'";

        $sql2 = "SELECT * 
                 FROM " . sql_table('users_status') . " 
                 WHERE uid='$userid'";

        if (!$result = sql_query($sql1)) {
            Error::code('0016');
        }

        if (!$myrow = sql_fetch_assoc($result)) {
            $myrow = array("uid" => 1);
        } else {
            $myrow = array_merge($myrow, (array)sql_fetch_assoc(sql_query($sql2)));
        }

        return $myrow;
    }
    
    /**
     * [getUserDataExtendFromId description]
     *
     * @param   [type]  $userid  [$userid description]
     *
     * @return  [type]           [return description]
     */
    public static function getUserDataExtendFromId($userid)
    {
        $sql1 = "SELECT * 
                 FROM " . sql_table('users_extend') . " 
                 WHERE uid='$userid'";
        /*   
        $sql2 = "SELECT * 
                 FROM ".sql_table('users_status')." 
                 WHERE uid='$userid'";
    
        if (!$result = sql_query($sql1)) {
            code('0016');
        }
    
        if (!$myrow = sql_fetch_assoc($result)) {
            $myrow = array( "uid" => 1);
        } else {
            $myrow = array_merge($myrow,(array) sql_fetch_assoc(sql_query($sql1)));
        }
        */

        $myrow = (array)sql_fetch_assoc(sql_query($sql1));
    
        return $myrow;
    }
    
    /**
     * [getUserData description]
     *
     * @param   [type]  $username  [$username description]
     *
     * @return  [type]             [return description]
     */
    public static function getUserData($username)
    {
        $sql = "SELECT * 
                FROM " . sql_table('users') . " 
                WHERE uname='$username'";

        if (!$result = sql_query($sql)) {
            Error::code('0016');
        }

        if (!$myrow = sql_fetch_assoc($result)) {
            $myrow = array("uid" => 1);
        }

        return $myrow;
    }

    /**
     * retourne un menu utilisateur 
     *
     * @param   [type]  $mns  [$mns description]
     * @param   [type]  $qui  [$qui description]
     *
     * @return  [type]        [return description]
     */
    public static function memberMenu($mns, $qui)
    {
        global $op;

        $ed_u = $op == 'edituser' ? 'active' : '';

        $cl_edj = $op == 'editjournal' ? 'active' : '';

        $cl_edh = $op == 'edithome' ? 'active' : '';

        $cl_cht = $op == 'chgtheme' ? 'active' : '';

        $cl_edjh = ($op == 'editjournal' or $op == 'edithome') ? 'active' : '';

        $cl_u = $_SERVER['REQUEST_URI'] == '/user.php' ? 'active' : '';

        $cl_pm = strstr($_SERVER['REQUEST_URI'], '/viewpmsg.php') ? 'active' : '';

        $cl_rs = ($_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux' or $_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux&op=EditReseaux') ? 'active' : '';
        
        echo '
        <ul class="nav nav-tabs d-flex flex-wrap"> 
            <li class="nav-item"><a class="nav-link ' . $cl_u . '" href="user.php" title="' . translate("Votre compte") . '" data-bs-toggle="tooltip" ><i class="fas fa-user fa-2x d-xl-none"></i><span class="d-none d-xl-inline"><i class="fas fa-user fa-lg"></i></span></a></li>
            <li class="nav-item"><a class="nav-link ' . $ed_u . '" href="user.php?op=edituser" title="' . translate("Vous") . '" data-bs-toggle="tooltip" ><i class="fas fa-user-edit fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate("Vous") . '</span></a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass ' . $cl_edjh . '" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" data-bs-html="true" title="' . translate("Editer votre journal") . '<br />' . translate("Editer votre page principale") . '"><i class="fas fa-edit fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">Editer</span></a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item ' . $cl_edj . '" href="user.php?op=editjournal" title="' . translate("Editer votre journal") . '" data-bs-toggle="tooltip">' . translate("Journal") . '</a></li>
                    <li><a class="dropdown-item ' . $cl_edh . '" href="user.php?op=edithome" title="' . translate("Editer votre page principale") . '" data-bs-toggle="tooltip">' . translate("Page") . '</a></li>
                </ul>
            </li>';

        include("modules/upload/Config/upload.conf.php");

        if (($mns) and ($autorise_upload_p)) {
            include_once("modules/blog/Controllers/upload_minisite.php");

            $PopUp = win_upload("popup");

            echo '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="' . translate("Gérer votre miniSite") . '"><i class="fas fa-desktop fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">' . translate("MiniSite") . '</span></a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="minisite.php?op=' . $qui . '" target="_blank">' . translate("MiniSite") . '</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="window.open(' . $PopUp . ')" >' . translate("Gérer votre miniSite") . '</a></li>
                </ul>
            </li>';
        }

        echo '
            <li class="nav-item"><a class="nav-link ' . $cl_cht . '" href="user.php?op=chgtheme" title="' . translate("Changer le thème") . '"  data-bs-toggle="tooltip" ><i class="fas fa-paint-brush fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate("Thème") . '</span></a></li>
            <li class="nav-item"><a class="nav-link ' . $cl_rs . '" href="modules.php?ModPath=reseaux-sociaux&amp;ModStart=reseaux-sociaux" title="' . translate("Réseaux sociaux") . '"  data-bs-toggle="tooltip" ><i class="fas fa-share-alt-square fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate("Réseaux sociaux") . '</span></a></li>
            <li class="nav-item"><a class="nav-link ' . $cl_pm . '" href="viewpmsg.php" title="' . translate("Message personnel") . '"  data-bs-toggle="tooltip" ><i class="far fa-envelope fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate("Message") . '</span></a></li>
            <li class="nav-item"><a class="nav-link " href="user.php?op=logout" title="' . translate("Déconnexion") . '" data-bs-toggle="tooltip" ><i class="fas fa-sign-out-alt fa-2x text-danger d-xl-none"></i><span class="d-none d-xl-inline text-danger">&nbsp;' . translate("Déconnexion") . '</span></a></li>
        </ul>
        <div class="mt-3"></div>';
    }

}