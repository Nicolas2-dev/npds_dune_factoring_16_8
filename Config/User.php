<?php

/**
 * User configuration.
 */
return array(
      
    /**
     * Duration in hour for Admin cookie (default 24)
     */
    'user_cook_duration' => 8000,

    /**
     * Anonymous users Default Name
     */
    'anonymous' => 'Visiteur',

    /**
     * Minimum character for users passwords
     */
    'minpass' => 8,

    /**
     * Number off user showed in memberslist page
     */
    'show_user' => 20,

    /**
     * Activate Avatar? (1=Yes 0=No)
     */
    'smilies' => 1,

    /**
     * Maximum size for uploaded avatars in pixel (width*height) 
     */
    'avatar_size' => '80*100',

    /**
     * Activate Short User registration (without ICQ, MSN, ...)? (1=Yes 0=No)
     */
    'short_user' => 1,

    /**
     * Make the members List Private (only for members) or Public (Private=Yes Public=No)
     */
    'member_list' => 1,

    /**
     * Allow automated new-user creation (sending email and allowed connection)
     */
    'auto_reg_user' => 1,

    /**
     * Allow members to hide from other members, ... (1=Yes, 0=no)
     */
    'member_invisible' => 0,

    /**
     * Allow you to close New Member Registration (from Gawax Idea), ... (1=Yes, 0=no)
     */
    'close_reg_user' => 0,

    /**
     * Allow user to choose alone the password (1=Yes, 0=no)
     */
    'memberpass' => 1,

);