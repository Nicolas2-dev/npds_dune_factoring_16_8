<?php


use Npds\Config\Config;
use Npds\Support\Facades\Password;


if ((isset($aid)) and (isset($pwd)) and ($op == 'login')) {

    if ($aid != '' and $pwd != '') {

        $result = sql_query("SELECT pwd, hashkey FROM " . sql_table('authors') . " WHERE aid='$aid'");

        if (sql_num_rows($result) == 1) {

            $setinfo = sql_fetch_assoc($result);
            $dbpass = $setinfo['pwd'];

            // deprecated function utf8_decode !
            // $pwd = utf8_decode($pwd);  

            $scryptPass = null;

            if (password_verify($pwd, $dbpass) or (strcmp($dbpass, $pwd) == 0)) {
                if (!$setinfo['hashkey']) {

                    $pwd = Password::password_crypt($pwd);

                    sql_query("UPDATE " . sql_table('authors') . " SET pwd='$pwd', hashkey='1' WHERE aid='$aid'");
                    $result = sql_query("SELECT pwd, hashkey FROM " . sql_table('authors') . " WHERE aid = '$aid'");

                    if (sql_num_rows($result) == 1) {
                        $setinfo = sql_fetch_assoc($result);
                    }

                    $dbpass = $setinfo['pwd'];
                    $scryptPass = crypt($dbpass, $hashpass);
                }
            }

            if (password_verify($pwd, $dbpass)) {
                $CryptpPWD = $dbpass;
            } elseif (password_verify($dbpass, $scryptPass) or strcmp($dbpass, $pwd) == 0) {
                $CryptpPWD = $pwd;
            } else {
                Admin_Alert("Passwd not in DB#1 : $aid");
            }

            $admin = base64_encode("$aid:" . md5($CryptpPWD));

            $admin_cook_duration = Config::get('author.admin_cook_duration');

            if ($admin_cook_duration <= 0) {
                $admin_cook_duration = 1;
            }

            $timeX = time() + (3600 * $admin_cook_duration);

            setcookie('admin', $admin, $timeX);
            setcookie('adm_exp', $timeX, $timeX);
        }
    }
}

/**
 * $super_admintest : permet de savoir si un admin est connect&ecute; 
 * ($admintest=true) et s'il est SuperAdmin ($super_admintest=true) 
 */
$admintest = false;
$super_admintest = false;

if (isset($admin) and ($admin != '')) {
    $Xadmin = base64_decode($admin);
    $Xadmin = explode(':', $Xadmin);

    $aid = urlencode($Xadmin[0]);
    $AIpwd = $Xadmin[1];

    if ($aid == '' or $AIpwd == '') {
        Admin_Alert('Null Aid or Passwd');
    }

    $result = sql_query("SELECT pwd, radminsuper FROM " . sql_table('authors') . " WHERE aid = '$aid'");

    if (!$result) {
        Admin_Alert("DB not ready #2 : $aid / $AIpwd");
    } else {
        list($AIpass, $Xsuper_admintest) = sql_fetch_row($result);

        if (md5($AIpass) == $AIpwd and $AIpass != '') {
            $admintest = true;
            $super_admintest = $Xsuper_admintest;
        } else {
            Admin_Alert("Password in Cookies not Good #1 : $aid / $AIpwd");
        }
    }

    unset($AIpass);
    unset($AIpwd);
    unset($Xadmin);
    unset($Xsuper_admintest);
}