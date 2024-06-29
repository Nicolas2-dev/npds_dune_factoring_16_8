<?php

namespace Npds\Editeur;

use Npds\Support\Facades\Language;
use Npds\Contracts\Editeur\EditeurInterface;


/**
 * Editeur class
 */
class Editeur implements EditeurInterface
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
     * Charge l'éditeur ... ou non : $Xzone = nom du textarea
     * 
     * si $Xzone="custom" on utilise $Xactiv pour passer des paramètres spécifiques
     *
     * @param   [type]  $Xzone   [$Xzone description]
     * @param   [type]  $Xactiv  [$Xactiv description]
     *
     * @return  [type]           [return description]
     */
    public static function fetch($Xzone, $Xactiv)
    {
        global $language, $tmp_theme, $tiny_mce, $tiny_mce_theme, $tiny_mce_relurl;

        $tmp = '';
        
        if ($tiny_mce) {
            static $tmp_Xzone;

            if ($Xzone == 'tiny_mce') {
                if ($Xactiv == 'end') {
                    if (substr((string) $tmp_Xzone, -1) == ',') {
                        $tmp_Xzone = substr_replace((string) $tmp_Xzone, '', -1);
                    }

                    if ($tmp_Xzone) {
                        $tmp = "
                        <script type=\"text/javascript\">
                        //<![CDATA[
                            document.addEventListener(\"DOMContentLoaded\", function(e) {
                                tinymce.init({
                                selector: 'textarea.tin',
                                mobile: {menubar: true},
                                language : '" . Language::language_iso(1, '', '') . "',";

                        include("assets/shared/editeur/tinymce/themes/advanced/npds.conf.php");

                        $tmp .= '
                                });
                            });
                        //]]>
                        </script>';
                    }
                } else {
                    $tmp .= '<script type="text/javascript" src="assets/shared/editeur/tinymce/tinymce.min.js"></script>';
                }
            } else {
                $tmp_Xzone .= $Xzone != 'custom' ? $Xzone . ',' : $Xactiv . ',';
            }
        } else {
            $tmp = '';
        }

        return $tmp;
    }

}