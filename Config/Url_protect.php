<?php

/**
 * Url Protect configuration.
 */
return array(
    
    /**
     * 
     */
    'bad_uri_content' => array(
        // To Filter "php WebWorm" and like Santy and other
        "perl",
        "chr(",

        // To prevent SQL-injection
        " union ",
        " into ",
        " select ",
        " update ",
        " from ",
        " where ",
        " insert ",
        " drop ",
        " delete ",
        
        // Comment inline SQL - shiney 2011
        "/*",

        // To prevent XSS
        "outfile",
        "/script",
        "url(",
        "/object",
        "img dynsrc",
        "img lowsrc",
        "/applet",
        "/style",
        "/iframe",
        "/frameset",
        "document.cookie",
        "document.location",
        "msgbox(",
        "alert(",
        "expression(",

        // some HTML5 tags - dev 2012
        "formaction",
        "autofocus",
        "onforminput",
        "onformchange",
        "history.pushstate("
    ),

    /**
     * 
     */
    'bad_uri_name' => array(
        'GLOBALS', 
        '_SERVER', 
        '_REQUEST', 
        '_GET', 
        '_POST', 
        '_FILES', 
        '_ENV', 
        '_COOKIE', 
        '_SESSION'
    ),
);
