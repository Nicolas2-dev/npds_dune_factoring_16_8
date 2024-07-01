<?php

use Npds\Support\Facades\Theme;
use Npds\Support\Facades\Editeur;


global $tiny_mce, $Default_Theme;
//if ($tiny_mce){
    echo Editeur::fetch('tiny_mce', 'end');
//}

// include externe file from modules/include for functions, codes ...
if (file_exists("Themes/default/include/footer_before.inc")) {
    include("Themes/default/include/footer_before.inc");
}

$cookie9 = Theme::foot();

// include externe file from modules/themes include for functions, codes ...
if (isset($user)) {
    if (file_exists("themes/$cookie9/include/footer_after.inc")) {
        include("themes/$cookie9/include/footer_after.inc");
    } else {
        if (file_exists("themes/default/include/footer_after.inc")) {
            include("Themes/default/include/footer_after.inc");
        }
    }
} else {
    if (file_exists("themes/$Default_Theme/include/footer_after.inc")) { 
        include("themes/$Default_Theme/include/footer_after.inc");
    } else {
        if (file_exists("Themes/default/include/footer_after.inc")) {
            include("Themes/default/include/footer_after.inc");
        }
    }
}

echo '
      </body>
   </html>';

include("sitemap.php");

// Ferme la connexion avec la Base de données
sql_close();