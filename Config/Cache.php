<?php

/**
 * Cache configuration.
 */
return array(
    
    /**
     * 
     */
    'super_cache' => false,

    /**
     * Be sure that apache user have the permission to Read/Write/Delete in the Dir
     */
    'data_dir' => 'cache/',

    /**
     * How the Auto_Cleanup process is run
     * 0 no cleanup - 1 auto_cleanup
     */
    'run_cleanup'  => 1,

    /**
     * value between 1 and 100. The most important is the value, the most "probabilidad", cleanup process as chance to be runed
     */
    'cleanup_freq' => 20,

    /**
     * maximum age - 24 Hours
     */
    'max_age' => 86400,

    /**
     * Instant Stats
     * 0 no - 1 Yes
     */
    'save_stats' => 0,

    /**
     * Terminate send http process after sending cache page
     * 0 no - 1 Yes
     */
    'exit' => 0,

    /**
     * If the maximum number of "webuser" is ritched : SuperCache not clean the cache
     * compare with the value store in cache/site_load.log updated by the site_load() function of mainfile.php
     */
    'clean_limit' => 300,

    /**
     * Same standard cache (not the functions for members) for anonymous and members
     * 0 no - 1 Yes
     */
    'non_differentiate' => 0,


    'cache_page' => array(
        
        'index.php' => array(
            'timings'   => 300,
            'query'     => '^',
        ),
        
        'article.php' => array(
            'timings'   => 300,
            'query'     => '^',
        ),
        
        'sections.php' => array(
            'timings'   => 300,
            'query'     => '^op',
        ),

        'faq.php' => array(
            'timings'   => 86400,
            'query'     => '^myfaq',
        ),

        'links.php' => array(
            'timings'   => 28800,
            'query'     => '^',
        ),

        'forum.php' => array(
            'timings'   => 3600,
            'query'     => '^',
        ),

        'memberslist.php' => array(
            'timings'   => 1800,
            'query'     => '^',
        ),

        'modules.php' => array(
            'timings'   => 3600,
            'query'     => '^',
        ),
    )
);    