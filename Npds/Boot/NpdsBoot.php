<?php


namespace Npds\Boot;


class NpdsBoot 
{

	/**
	 * [$npds_boot_strappers description]
	 *
	 * @var [type]
	 */
	protected $npds_boot_strappers = [
        'Npds\Boot\Bootstrap\CheckInstall',
        // 'Npds\Boot\Bootstrap\LoadConfig',
        // 'Npds\Boot\Bootstrap\DebugMode',		
        // 'Npds\Boot\Bootstrap\LoadAliases',
        'Npds\Boot\Bootstrap\LoadSpamBoot',
        'Npds\Boot\Bootstrap\MetaCharset',
        'Npds\Boot\Bootstrap\InitLanguage',
	];

	/**
	 * [$npds_has_been_bootstrapped description]
	 *
	 * @var [type]
	 */
	protected $npds_has_been_bootstrapped = false;


	/**
	 * [__construct description]
	 *
	 * @return  [type]  [return description]
	 */
	public function __construct()
	{
        $this->bootstrap();
	}

	/**
	 * [bootstrap description]
	 *
	 * @return  [type]  [return description]
	 */
	public function bootstrap()
	{
		if ( ! $this->npds_has_been_bootstrapped())
		{
			$this->bootstrap_with($this->boot_strappers());
		}
	}

	/**
	 * [bootstrap_with description]
	 *
	 * @param   array  $bootstrappers  [$bootstrappers description]
	 *
	 * @return  [type]                 [return description]
	 */
	public function bootstrap_with(array $bootstrappers)
	{
		$this->npds_has_been_bootstrapped = true;

		foreach ($bootstrappers as $bootstrapper)
		{
			(new $bootstrapper)->bootstrap($this);
		}
	}

	/**
	 * [npds_has_been_bootstrapped description]
	 *
	 * @return  [type]  [return description]
	 */
	public function npds_has_been_bootstrapped()
	{
		return $this->npds_has_been_bootstrapped;
	}

	/**
	 * [boot_strappers description]
	 *
	 * @return  [type]  [return description]
	 */
	protected function boot_strappers()
	{
		return $this->npds_boot_strappers;
	}

}