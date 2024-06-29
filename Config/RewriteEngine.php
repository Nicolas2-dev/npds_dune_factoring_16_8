<?php

/**
 * Rewrite Engine configuration.
 */
return array(

    /**
     * Exemple :
     */
    'output' => function ($output) {
        return preg_replace(' class="noir"', '', $output);
    },

);
