<?php


namespace Npds\Boot\Bootstrap;

use Npds\Debug\Debug;
use Npds\Config\Config;
use Npds\Execption\ExecptionHandler;


class DebugMode
{

	public function bootstrap()
	{
		// changer la valeur a true pour activé le debugeur false pour désactivé.
		if (Config::get('debug')) {

			// Modify the report level of PHP
			 Debug::error_reporting('all');
			error_reporting(-1);

			ini_set('display_errors', 'Off');   

			// Initialize the Exceptions Handler.
			ExecptionHandler::initialize();
		}
	}

}