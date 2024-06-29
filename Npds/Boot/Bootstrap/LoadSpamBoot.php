<?php


namespace Npds\Boot\Bootstrap;


use Npds\Utility\Spam;


class LoadSpamBoot
{

	public function bootstrap()
	{
        //
        Spam::spam_log();
	}

}