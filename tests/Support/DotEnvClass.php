<?php namespace Lanin\Laravel\SetupWizard\Tests\Support;

use Lanin\Laravel\SetupWizard\Support\DotEnv;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class DotEnvClass extends TestCase
{
	/** @test */
	public function it_caches_env_variables()
	{
		DotEnv::load($this->getFixturePath(), '.env.example');

		$this->assertAttributeNotEmpty('variables', DotEnv::class);

		$this->assertEquals(16, count(DotEnv::$variables));
	}
}