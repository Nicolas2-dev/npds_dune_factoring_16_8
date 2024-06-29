<?php

/**
 * Pollbooth configuration.
 */
return array(

    /**
     * Number of maximum options for each poll
     */
    'maxOptions' => 12,

    /**
     * Set cookies to prevent visitors vote twice in a period of 24 hours? (0=Yes 1=No)
     */
    'setCookies' => 1,

    /**
     * Activate comments in Polls? (1=Yes 0=No)
     */
    'pollcomm' => 1,

    // Specified the index and the name of the application for the table appli_log
    
    /**
     * [description]
     */
    'al_id' => 1,
    
    /**
     * [description]
     */
    'al_nom' => 'Poll',

);
