<?php


namespace Npds\Boot\Bootstrap;


class MetaCharset
{

	public function bootstrap()
	{
        // include current charset
        if (file_exists("storage/meta/cur_charset.php")) { 
            include("storage/meta/cur_charset.php");
        }
	}

}