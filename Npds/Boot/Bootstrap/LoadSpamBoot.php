<?php


namespace Npds\Boot\Bootstrap;


use Npds\Utility\Spam;


class LoadSpamBoot
{

	/**
	 * [bootstrap description]
	 *
	 * @return  [type]  [return description]
	 */
	public function bootstrap()
	{
        //
        Spam::spam_log();
	}

}