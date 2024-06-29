<?php


namespace Npds\Boot\Bootstrap;


use Npds\Boot\AliasLoader;


class LoadAliases
{

	/**
	 * [bootstrap description]
	 *
	 * @return  [type]  [return description]
	 */
	public function bootstrap()
	{
        // Initialize the Aliases Loader.
        AliasLoader::initialize();
	}

}