<?php

namespace Npds\Boot;

use Npds\Config\Config;

use RuntimeException;


class AliasLoader
{

    /**
     * Bootstrap the Aliases Loader.
     *
     * @return void
     */
    public static function initialize()
    {
        $classes = Config::get('aliases', array());

        foreach ($classes as $classAlias => $className) {
            // Cela garantit que l'alias est créé dans l'espace de noms global.
            $classAlias = '\\' .ltrim($classAlias, '\\');

            // Vérifiez si la classe existe déjà.
            if (class_exists($classAlias)) {
                // une classe existe déjà avec le même nom.
                throw new RuntimeException('A class [' .$classAlias .'] already exists with the same name.');
            }

            class_alias($className, $classAlias);
        }
    }
}