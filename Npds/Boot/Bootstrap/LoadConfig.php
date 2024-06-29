<?php


namespace Npds\Boot\Bootstrap;

use Npds\Config\Config;


class LoadConfig
{

    /**
     * [bootstrap description]
     *
     * @return  [type]  [return description]
     */
	public function bootstrap()
	{
        // Load the configuration files.
        foreach (glob('Config/*.php') as $path) {
            $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));
            
            Config::set($key, require($path));
        }

        //include("Config/Config.php");
	}

}