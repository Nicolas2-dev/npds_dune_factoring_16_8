<?php

use Npds\Support\Facades\Code;
use Npds\Support\Facades\Language;
use Npds\Support\Facades\Metalang;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

// settype($npds, 'integer');
// settype($op, 'string');
// settype($metalang, 'integer');
// settype($nl, 'integer');

$pdst = $npds;
$remp = '';

include("header.php");

echo '<div id="static_cont">';

if (($op != '') and ($op)) {
    // Troll Control for security
    if (preg_match('#^[a-z0-9_\.-]#i', $op) 
    and !stristr($op, ".*://") 
    and !stristr($op, "..") 
    and !stristr($op, "../") 
    and !stristr($op, "script") 
    and !stristr($op, "cookie") 
    and !stristr($op, "iframe") 
    and  !stristr($op, "applet") 
    and !stristr($op, "object") 
    and !stristr($op, "meta")) 
    {
        if (file_exists("static/$op")) {
            if (!$metalang and !$nl) {
                include("static/$op");
            } else {
                ob_start();
                    include("static/$op");
                    $remp = ob_get_contents();
                ob_end_clean();

                if ($metalang) {
                    $remp = Metalang::meta_lang(Code::aff_code(Language::aff_langue($remp)));
                }

                if ($nl){
                    $remp = nl2br(str_replace(' ', '&nbsp;', htmlentities($remp, ENT_QUOTES, cur_charset)));
                }

                echo $remp;
            }

            echo '<div class=" my-3"><a href="print.php?sid=static:' . $op . '&amp;metalang=' . $metalang . '&amp;nl=' . $nl . '" data-bs-toggle="tooltip" data-bs-placement="right" title="' . translate("Page spéciale pour impression") . '"><i class="fa fa-2x fa-print"></i></a></div>';

            // Si vous voulez tracer les appels au pages statiques : supprimer les // devant la ligne ci-dessous
            // Ecr_Log("security", "static/$op", "");
        } else {
            echo '<div class="alert alert-danger">' . translate("Merci d'entrer l'information en fonction des spécifications") . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">' . translate("Merci d'entrer l'information en fonction des spécifications") . '</div>';
    }
}

echo '</div>';

include("footer.php");
