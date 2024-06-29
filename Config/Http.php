<?php

/**
 * HTTP configuration.
 */
return array(

    /**
     * Activate the validation of the existance of a web on Port 80 for Headlines (true=Yes false=No)
     */
    'rss_host_verif' => false,

    /**
     * Activate the Advance Caching Meta Tag (pragma ...) (true=Yes false=No)
     */
    'cache_verif' => true,

    /**
     * Activate the DNS resolution for posts (forum ...), IP-Ban, ... (true=Yes false=No)
     */
    'dns_verif' => false,

    /**
     * Referer Option
     */
    'referer' => array(

        /**
         * Activate HTTP referer logs to know who is linking to our site? (1=Yes 0=No)
         */
        'httpref' => 1,

        /**
         * Maximum number of HTTP referers to store in the Database (Try to not set this to a high number, 500 ~ 1000 is Ok)
         */
        'httprefmax' => 1000,
    )
);
