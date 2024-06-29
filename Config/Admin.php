<?php

/**
 * Admin configuration.
 */
return array(

    /**
     * Some Graphics Options
     */
    'options' => array(

        /**
         * Topics images path (put / only at the end, not at the begining)
         */
        'tipath' => 'images/topics/',
        
        /**
         * User images path (put / only at the end, not at the begining)
         */
        'userimg' => 'images/menu/',

        /**
         * Administration system images path (put / only at the end, not at the begining)
         */
        'adminimg' => 'images/admin/',

        /**
         * Activate short Administration Menu? (1=Yes 0=No)
         */
        'short_menu_admin' => 1,

        /**
         * Activate graphic menu for Administration Menu? (1=Yes 0=No)
         */
        'admingraphic' => 1,

        /**
         * Image Files'extesion for admin menu (default: gif)
         */
        'admf_ext' => 'png',

        /**
         * How many articles to show in the admin section?
         */
        'admart' => 10,
    )

);