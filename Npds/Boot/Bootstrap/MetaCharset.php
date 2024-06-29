<?php


namespace Npds\Boot\Bootstrap;


class MetaCharset
{

    /**
     * [bootstrap description]
     *
     * @return  [type]  [return description]
     */
	public function bootstrap()
	{
        // include current charset
        if (file_exists("storage/meta/cur_charset.php")) { 
            include("storage/meta/cur_charset.php");
        }
	}

}