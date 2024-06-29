<?php

/**
 * Theme configuration.
 */
return array(

    /**
     * Default Theme for your site (See /themes directory for the complete list, case sensitive!)
     */
    'default_theme' => 'TwoBoost',

    /**
     * Default Skin for Theme ... with skins (See /themes/_skins directory for the complete list, case sensitive!)
     */
    'default_skin' => 'default',

    /**
     * Messages for all footer pages (Can include HTML code)
     */
    'footer' => array(

        /**
         * $foot(1)
         */
        'foot1' => "<a href=\"admin.php\" ><i class=\"fa fa-cogs fa-2x me-3 align-middle\" title=\"Admin\" data-bs-toggle=\"tooltip\"></i></a>
         <a href=\"https://www.mozilla.org/fr/\" target=\"_blank\"><i class=\"fab fa-firefox fa-2x  me-1 align-middle\"  title=\"get Firefox\" data-bs-toggle=\"tooltip\"></i></a>
         <a href=\"static.php?op=charte.html&amp;npds=0&amp;metalang=1\">Charte</a> 
         - <a href=\"modules.php?ModPath=contact&amp;ModStart=contact\" class=\"me-3\">Contact</a> 
         <a href=\"backend.php\" target=\"_blank\" ><i class=\"fa fa-rss fa-2x  me-3 align-middle\" title=\"RSS 1.0\" data-bs-toggle=\"tooltip\"></i></a>&nbsp;
        <a href=\"https://github.com/npds/npds_dune\" target=\"_blank\"><i class=\"fab fa-github fa-2x  me-3 align-middle\"  title=\"NPDS Dune on Github ...\" data-bs-toggle=\"tooltip\"></i></a>",

        /**
         * $foot(2)
         */
        'foot2' => "Tous les Logos et Marques sont d&eacute;pos&eacute;s, les commentaires sont sous la responsabilit&eacute; de ceux qui les ont publi&eacute;s, le reste &copy; <a href=\"http://www.npds.org\" target=\"_blank\" >NPDS</a>",

        /**
         * $foot(3)
         */
        'foot3' => '',

        /**
         * $foot(4)
         */
        'foot4' => '',
    ),

);