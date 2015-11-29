<?php namespace Lanin\Laravel\SetupWizard\Support;

use Illuminate\Contracts\Foundation\Application;

class LoadConfiguration extends \Illuminate\Foundation\Bootstrap\LoadConfiguration {

	/**
	 * Update settings in the existing config repository.
	 *
	 * @param Application $app
	 */
	public function bootstrap(Application $app)
	{
		$this->loadConfigurationFiles($app, $app['config']);
	}

}