<?php


namespace Npds\Boot;


class NpdsBoot 
{

	protected $npds_boot_strappers = [
        'Npds\Boot\Bootstrap\CheckInstall',
        // 'Npds\Boot\Bootstrap\LoadConfig',
        // 'Npds\Boot\Bootstrap\DebugMode',		
        // 'Npds\Boot\Bootstrap\LoadAliases',
        'Npds\Boot\Bootstrap\LoadSpamBoot',
        'Npds\Boot\Bootstrap\MetaCharset',
        'Npds\Boot\Bootstrap\InitLanguage',
	];

	protected $npds_has_been_bootstrapped = false;


	public function __construct()
	{
        $this->bootstrap();
	}

	public function bootstrap()
	{
		if ( ! $this->npds_has_been_bootstrapped())
		{
			$this->bootstrap_with($this->boot_strappers());
		}
	}

	public function bootstrap_with(array $bootstrappers)
	{
		$this->npds_has_been_bootstrapped = true;

		foreach ($bootstrappers as $bootstrapper)
		{
			(new $bootstrapper)->bootstrap($this);
		}
	}

	public function npds_has_been_bootstrapped()
	{
		return $this->npds_has_been_bootstrapped;
	}

	protected function boot_strappers()
	{
		return $this->npds_boot_strappers;
	}

}