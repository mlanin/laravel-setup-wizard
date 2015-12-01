<?php namespace Lanin\Laravel\SetupWizard\Tests;

use Lanin\Laravel\SetupWizard\SetupWizardServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();
		// Your code here
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{


		/** @var \Illuminate\Config\Repository $config */
		$config = $app['config'];

		$config->set('database.default', 'testing');
		$config->set('database.connections.testing', [
				'driver'   => 'sqlite',
				'database' => ':memory:',
				'prefix'   => '',
		]);
	}

	/**
	 * Get package providers.
	 *
	 * @param  \Illuminate\Foundation\Application $app
	 *
	 * @return array
	 */
	protected function getPackageProviders($app)
	{
		return [
			SetupWizardServiceProvider::class,
		];
	}

	/**
	 * Get the migrations source path.
	 *
	 * @param  string $path
	 * @return string
	 */
	protected function getFixturePath($path = '')
	{
		return realpath(dirname(__DIR__) . '/tests/fixture') . $path;
	}
}