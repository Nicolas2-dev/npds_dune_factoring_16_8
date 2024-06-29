<?php

use Npds\Support\Facades\Str;
use Npds\Support\Facades\Request;
use Npds\Support\Facades\Messenger;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

switch (Request::input('op')) 
{
        // Instant Members Message
    case 'instant_message':
        Messenger::Form_instant_message($to_userid);
        break;

    case 'write_instant_message':

        // settype($copie, 'string');
        // settype($messages, 'string');

        if (isset($user)) {
            $rowQ1 = Q_Select("SELECT uid 
                               FROM " . sql_table('users') . " 
                               WHERE uname='$cookie[1]'", 3600);
                               
            $uid = $rowQ1[0];

            $from_userid = $uid['uid'];

            if (($subject != '') or ($message != '')) {
                $subject    = Str::FixQuotes($subject) . '';
                $messages   = Str::FixQuotes($messages) . '';

                Messenger::writeDB_private_message($to_userid, '', $subject, $from_userid, $message, $copie);
            }
        }

        Header("Location: index.php");
        break;
}
