<?php namespace Lanin\Laravel\SetupWizard;

use Illuminate\Support\ServiceProvider;
use Lanin\Laravel\SetupWizard\Commands\Setup;

class SetupWizardServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap application service.
	 */
	public function boot()
	{

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('setup-wizard.setup', function($app)
		{
			return $app[Setup::class];
		});

        $this->commands('setup-wizard.setup');
	}
}