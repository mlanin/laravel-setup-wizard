<?php namespace Lanin\Laravel\SetupWizard\Tests\Support\Bootstrap;

use Lanin\Laravel\SetupWizard\Support\Bootstrap\DetectEnvironment;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class DetectEnvironmentTest extends TestCase
{
	/** @var DetectEnvironment */
	private $bootstrapper;

	public function setUp()
	{
		parent::setUp();

		foreach(['APP_DEBUG' => 'false', 'APP_ENV' => 'production', 'APP_KEY' => 'SomeRandomString'] as $name => $value)
		{
			putenv("$name=$value");
			$_ENV[$name] = $value;
			$_SERVER[$name] = $value;
		}

		$this->bootstrapper = new DetectEnvironment();
	}
	public function tearDown()
	{
		parent::tearDown();

		foreach (['APP_DEBUG', 'APP_ENV', 'APP_KEY'] as $name)
		{
			unset($_ENV[$name]);
			unset($_SERVER[$name]);
		}
		unset($this->bootstrapper);
	}

	/** @test */
	public function it_has_bootstrap_method()
	{
		$this->assertTrue(
				is_callable([$this->bootstrapper, 'bootstrap']),
				'Boostrapper doesn\'t have bootstrap() method'
		);
	}

	/** @test */
	public function it_replaces_already_set_variables()
	{
		$this->app->useEnvironmentPath($this->getFixturePath());

		$this->app->bootstrapWith([
				DetectEnvironment::class,
		]);

		$this->assertTrue(env('APP_DEBUG'));
		$this->assertEquals('test', env('APP_ENV'));
		$this->assertTrue($this->app->environment('test'));
	}
}