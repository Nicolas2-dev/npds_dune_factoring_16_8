<?php

namespace Npds\Encryption;

use Npds\Config\Config;
use Npds\Contracts\Encryption\CryptInterface;


/**
 * Crypt class
 */
class Crypt implements CryptInterface
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
     * Composant des fonctions encrypt et decrypt
     *
     * @param   [type]  $txt          [$txt description]
     * @param   [type]  $encrypt_key  [$encrypt_key description]
     *
     * @return  [type]                [return description]
     */
    public static function keyED($txt, $encrypt_key)
    {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1);
            $ctr++;
        }

        return $tmp;
    }

    /**
     * retourne une chaine encryptée en utilisant la valeur de $NPDS_Key
     *
     * @param   [type]  $txt  [$txt description]
     *
     * @return  [type]        [return description]
     */
    public static function encrypt($txt)
    {
        return static::encryptK($txt, Config::get('npds.npds_key'));
    }
 
    /**
     * retourne une chaine encryptée en utilisant la clef : $C_key
     *
     * @param   [type]  $txt    [$txt description]
     * @param   [type]  $C_key  [$C_key description]
     *
     * @return  [type]          [return description]
     */
    public static function encryptK($txt, $C_key)
    {
        $rand = (float) microtime();

        srand($rand * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) { 
                $ctr = 0;
            }

            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));
            $ctr++;
        }

        return base64_encode(static::keyED($tmp, $C_key));
    }

    /**
     * retourne une chaine décryptée en utilisant la valeur de $NPDS_Key
     *
     * @param   [type]  $txt  [$txt description]
     *
     * @return  [type]        [return description]
     */
    public static function decrypt($txt)
    {
        return (static::decryptK($txt, Config::get('npds.npds_key')));
    }

    /**
     * retourne une décryptée en utilisant la clef de $C_Key
     *
     * @param   [type]  $txt    [$txt description]
     * @param   [type]  $C_key  [$C_key description]
     *
     * @return  [type]          [return description]
     */
    public static function decryptK($txt, $C_key)
    {
        $txt = static::keyED(base64_decode($txt), $C_key);
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = substr($txt, $i, 1);
            $i++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }

        return $tmp;
    }

}