<?php


namespace Npds\Boot\Bootstrap;


use Npds\Boot\AliasLoader;


class LoadAliases
{

	public function bootstrap()
	{
        // Initialize the Aliases Loader.
        AliasLoader::initialize();
	}

}