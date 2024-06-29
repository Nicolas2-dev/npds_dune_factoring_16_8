<?php

namespace Npds\Support;


/**
 * Npds class
 */
class Npds 
{

    /**
     * [npds_php_version description]
     *
     * @return  [type]  [return description]
     */
    public static function npds_php_version() 
    {
        global $Version_Num, $Version_Id, $Version_Sub;
    
        $minPhpVersion = '8.2'; 
        if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
            $message = sprintf(
                'Votre version de PHP doit être %s ou supérieure pour exécuter %s. Version actuelle: %s',
                $minPhpVersion,
                $Version_Id .' ' . $Version_Sub .' ' . $Version_Num, 
                PHP_VERSION
            );
    
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo $message;
    
            exit(1);
        }    
    }

    /**
     * [check_install description]
     *
     * @return  [type]  [return description]
     */
    public static function check_install() 
    {
        // Modification pour IZ-Xinstall - EBH - JPB & PHR
        if (file_exists("IZ-Xinstall.ok")) {
            if (file_exists("install.php") or is_dir("install")) {
                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <title>NPDS IZ-Xinstall - Installation Configuration</title>
                    </head>
                    <body>
                        <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #000000"><br />
                            NPDS IZ-Xinstall - Installation &amp; Configuration
                        </div>
                        <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #ff0000"><br />
                            Vous devez supprimer le r&eacute;pertoire "install" ET le fichier "install.php" avant de poursuivre !<br />
                            You must remove the directory "install" as well as the file "install.php" before continuing!
                        </div>
                    </body>
                </html>';
                die();
            }
        } else {
            if (file_exists("install.php") and is_dir("install"))
                header("location: install.php");
        }
    }

}