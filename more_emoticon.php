<?php

use Npds\Support\Facades\Css;
use Npds\Support\Facades\Smilies;


if (!function_exists("Mysql_Connexion")) {
    include("Bootstrap/Boot.php");
}

if (isset($user)) {
    if ($cookie[9] == '') {
        
    }

    if (isset($theme)) {
        $cookie[9] = $theme;
    }

    $tmp_theme = $cookie[9];

    if (!$file = @opendir("Themes/$cookie[9]")) {
        $tmp_theme = $Default_Theme;
    }
} else {
    $tmp_theme = $Default_Theme;
}

include('storage/meta/meta.php');

echo '<link rel="stylesheet" href="Themes/_skins/default/bootstrap.min.css">';

echo Css::importCss($tmp_theme, $language, '', '', '');

include('assets/formhelp.java.php');

echo '
    </head>
    <body class="p-2">
    ' . Smilies::putitems_more() . '
    </body>
</html>';
