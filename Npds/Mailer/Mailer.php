<?php

namespace Npds\Mailer;

use Exception;

use Npds\Support\Facades\Log;
use PHPMailer\PHPMailer\SMTP;
use Npds\Support\Facades\Spam;
use Npds\Support\Facades\Theme;
use PHPMailer\PHPMailer\PHPMailer;
use Npds\Contracts\Mailer\MailerInterface;


/**
 * Mailer class
 */
class Mailer implements MailerInterface
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
     * Pour envoyer un mail en texte ou html avec ou sans pieces jointes 
     * $mime = 'text', 'html' 'html-nobr'-(sans application de nl2br) ou 'mixed'-(avec piece(s) jointe(s) : génération ou non d'un DKIM suivant option choisie) 
     *
     * @param   [type] $email     [$email description]
     * @param   [type] $subject   [$subject description]
     * @param   [type] $message   [$message description]
     * @param   [type] $from      [$from description]
     * @param   [type] $priority  [$priority description]
     * @param   false  $mime      [$mime description]
     * @param   text   $file      [$file description]
     *
     * @return  [type]            [return description]
     */
    public static function send_email($email, $subject, $message, $from = "", $priority = false, $mime = "text", $file = null)
    {
        global $mail_fonction, $adminmail, $sitename, $NPDS_Key, $nuke_url;

        $From_email = $from != '' ? $from : $adminmail;

        if (preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $From_email)) {
            include 'lib/PHPMailer/PHPmailer.conf.php';
            
            if ($dkim_auto == 2) {
                //Private key filename for this selector 
                $privatekeyfile = 'lib/PHPMailer/key/' . $NPDS_Key . '_dkim_private.pem';

                //Public key filename for this selector 
                $publickeyfile = 'lib/PHPMailer/key/' . $NPDS_Key . '_dkim_public.pem';

                if (!file_exists($privatekeyfile)) {
                    //Create a 2048-bit RSA key with an SHA256 digest 
                    $pk = openssl_pkey_new(
                        [
                            'digest_alg' => 'sha256',
                            'private_key_bits' => 2048,
                            'private_key_type' => OPENSSL_KEYTYPE_RSA,
                        ]
                    );

                    //Save private key 
                    openssl_pkey_export_to_file($pk, $privatekeyfile);

                    //Save public key 
                    $pubKey = openssl_pkey_get_details($pk);
                    $publickey = $pubKey['key'];

                    file_put_contents($publickeyfile, $publickey);
                }
            }

            $debug = false;
            $mail = new PHPMailer($debug);

            try {
                //Server settings config smtp 
                if ($mail_fonction == 2) {
                    $mail->isSMTP();
                    $mail->Host       = $smtp_host;
                    $mail->SMTPAuth   = $smtp_auth;
                    $mail->Username   = $smtp_username;
                    $mail->Password   = $smtp_password;

                    if ($smtp_secure) {
                        if ($smtp_crypt === 'tls') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        } elseif ($smtp_crypt === 'ssl') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        }
                    }

                    $mail->Port       = $smtp_port;
                }

                $mail->CharSet = cur_charset;
                $mail->Encoding = 'base64';

                if ($priority) {
                    $mail->Priority = 2;
                }

                //Recipients 
                $mail->setFrom($adminmail, $sitename);
                $mail->addAddress($email, $email);

                //Content 
                if ($mime == 'mixed') {
                    $mail->isHTML(true);

                    // pièce(s) jointe(s)) 
                    if (!is_null($file)) {
                        if (is_array($file)) {
                            $mail->addAttachment($file['file'], $file['name']);
                        } else {
                            $mail->addAttachment($file);
                        }
                    }
                }

                if (($mime == 'html') or ($mime == 'html-nobr')) {
                    $mail->isHTML(true);

                    if ($mime != 'html-nobr') {
                        $message = nl2br($message);
                    }
                }

                $mail->Subject = $subject;
                $stub_mail = "<html>\n<head>\n<style type='text/css'>\nbody {\nbackground: #FFFFFF;\nfont-family: Tahoma, Calibri, Arial;\nfont-size: 1 rem;\ncolor: #000000;\n}\na, a:visited, a:link, a:hover {\ntext-decoration: underline;\n}\n</style>\n</head>\n<body>\n %s \n</body>\n</html>";
                
                if ($mime == 'text') {
                    $mail->isHTML(false);
                    $mail->Body = $message;
                } else {
                    $mail->Body = sprintf($stub_mail, $message);
                }

                if ($dkim_auto == 2) {
                    $mail->DKIM_domain = str_replace(['http://', 'https://'], ['', ''], $nuke_url);
                    $mail->DKIM_private = $privatekeyfile;;
                    $mail->DKIM_selector = $NPDS_Key;
                    $mail->DKIM_identity = $mail->From;
                }

                if ($mail_fonction == 2) {
                    if ($debug) {
                        // on génère un journal détaillé après l'envoi du mail 
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    }
                }

                $mail->send();

                if ($debug) {
                    // stop l'exécution du script pour affichage du journal sur la page 
                    die();
                }

                $result = true;
            } catch (Exception $e) {
                Log::Ecr_Log('smtpmail', "send Smtp mail by $email", "Message could not be sent. Mailer Error: $mail->ErrorInfo");
                $result = false;
            }
        }

        return $result ? true : false;
    }

    /**
     * Pour copier un subject+message dans un email ($to_userid)
     *
     * @param   [type]  $to_userid  [$to_userid description]
     * @param   [type]  $sujet      [$sujet description]
     * @param   [type]  $message    [$message description]
     *
     * @return  [type]              [return description]
     */
    public static function copy_to_email($to_userid, $sujet, $message)
    {
        $result = sql_query("SELECT email,send_email FROM " . sql_table('users') . " WHERE uid='$to_userid'");
        list($mail, $avertir_mail) = sql_fetch_row($result);

        if (($mail) and ($avertir_mail == 1)) {
            static::send_email($mail, $sujet, $message, '', true, 'html', '');
        }
    }

    /**
     * Appel la fonction d'affichage du groupe check_mail (theme principal de NPDS) sans class
     *
     * @param   [type]  $username  [$username description]
     *
     * @return  [type]             [return description]
     */
    public static function Mess_Check_Mail($username)
    {
        static::Mess_Check_Mail_interface($username, '');
    }

    /**
     * Affiche le groupe check_mail (theme principal de NPDS)
     *
     * @param   [type]  $username  [$username description]
     * @param   [type]  $class     [$class description]
     *
     * @return  [type]             [return description]
     */
    public static function Mess_Check_Mail_interface($username, $class)
    {
        global $anonymous;

        if ($ibid = Theme::theme_image("fle_b.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        }

        if ($class != "") {
            $class = "class=\"$class\"";
        }
        
        if ($username == $anonymous) {
            if ($imgtmp) {
                echo "<img alt=\"\" src=\"$imgtmp\" align=\"center\" />$username - <a href=\"user.php\" $class>" . translate("Votre compte") . "</a>";
            } else {
                echo "[$username - <a href=\"user.php\" $class>" . translate("Votre compte") . "</a>]";
            }
        } else {
            if ($imgtmp) {
                echo "<a href=\"user.php\" $class><img alt=\"\" src=\"$imgtmp\" align=\"center\" />" . translate("Votre compte") . "</a>&nbsp;" . static::Mess_Check_Mail_Sub($username, $class);
            } else {
                echo "[<a href=\"user.php\" $class>" . translate("Votre compte") . "</a>&nbsp;&middot;&nbsp;" . static::Mess_Check_Mail_Sub($username, $class) . "]";
            }
        }
    }

    /**
     * Affiche le groupe check_mail (theme principal de NPDS)
     *
     * @param   [type]  $username  [$username description]
     * @param   [type]  $class     [$class description]
     *
     * @return  [type]             [return description]
     */
    public static function Mess_Check_Mail_Sub($username, $class)
    {
        global $user;

        if ($username) {
            $userdata = explode(':', base64_decode($user));

            $total_messages = sql_num_rows(sql_query("SELECT msg_id FROM " . sql_table('priv_msgs') . " WHERE to_userid = '$userdata[0]' AND type_msg='0'"));
            $new_messages = sql_num_rows(sql_query("SELECT msg_id FROM " . sql_table('priv_msgs') . " WHERE to_userid = '$userdata[0]' AND read_msg='0' AND type_msg='0'"));
            
            if ($total_messages > 0) {
                if ($new_messages > 0) {
                    $Xcheck_Nmail = $new_messages;
                } else {
                    $Xcheck_Nmail = '0';
                }

                $Xcheck_mail = $total_messages;
            } else {
                $Xcheck_Nmail = '0';
                $Xcheck_mail = '0';
            }
        }

        $YNmail = "$Xcheck_Nmail";
        $Ymail = "$Xcheck_mail";
        $Mel = "<a href=\"viewpmsg.php\" $class>Mel</a>";

        if ($Xcheck_Nmail > 0) {
            $YNmail = "<a href=\"viewpmsg.php\" $class>$Xcheck_Nmail</a>";
            $Mel = 'Mel';
        }

        if ($Xcheck_mail > 0) {
            $Ymail = "<a href=\"viewpmsg.php\" $class>$Xcheck_mail</a>";
            $Mel = 'Mel';
        }

        return "$Mel : $YNmail / $Ymail";
    }
 
    /**
     * Contrôle si le domaine existe et si il dispose d'un serveur de mail
     *
     * @param   [type]  $email  [$email description]
     *
     * @return  [type]          [return description]
     */
    public static function checkdnsmail($email)
    {
        $ibid = explode('@', $email);

        if (!checkdnsrr($ibid[1], 'MX')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * utilisateur dans le fichier des mails incorrect true or false 
     *
     * @param   [type]  $utilisateur  [$utilisateur description]
     *
     * @return  [type]                [return description]
     */
    public static function isbadmailuser($utilisateur)
    {
        $contents = '';
        $filename = "storage/users_private/usersbadmail.txt";

        $handle = fopen($filename, "r");

        if (filesize($filename) > 0) {
            $contents = fread($handle, filesize($filename));
        }

        fclose($handle);
        
        if (strstr($contents, '#' . $utilisateur . '|')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [fakedmail description]
     *
     * @param   [type]  $r  [$r description]
     *
     * @return  [type]      [return description]
     */
    public static function fakedmail($r)
    {
        return Spam::preg_anti_spam($r[1]);
    }

}