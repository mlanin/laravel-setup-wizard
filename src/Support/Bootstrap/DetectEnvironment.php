<?php namespace Lanin\Laravel\SetupWizard\Support\Bootstrap;

use Dotenv;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;

class DetectEnvironment
{
	/**
	 * Bootstrap the given application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{
		try {
			Dotenv::makeMutable();
			Dotenv::load($app->environmentPath(), $app->environmentFile());
			Dotenv::makeImmutable();
		} catch (InvalidArgumentException $e) {
			//
		}

		$app->detectEnvironment(function () {
			return env('APP_ENV', 'production');
		});
	}
}
