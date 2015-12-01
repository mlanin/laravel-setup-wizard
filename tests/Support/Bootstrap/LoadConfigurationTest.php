<?php namespace Lanin\Laravel\SetupWizard\Tests\Support\Bootstrap;

use Lanin\Laravel\SetupWizard\Support\Bootstrap\DetectEnvironment;
use Lanin\Laravel\SetupWizard\Support\Bootstrap\LoadConfiguration;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class LoadConfigurationTest extends TestCase
{
    /** @var LoadConfiguration */
    private $bootstrapper;

    public function setUp()
    {
        parent::setUp();

        $this->bootstrapper = new LoadConfiguration();
    }

    public function tearDown()
    {
        parent::tearDown();

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
    public function it_reloads_configuration()
    {
        $this->app['config']['foo'] = 'bar';

        $this->app->useEnvironmentPath($this->getFixturePath());

        $this->app->bootstrapWith(
            [
                DetectEnvironment::class,
                LoadConfiguration::class,
            ]
        );

        $this->assertEquals('bar', config('foo'));
        $this->assertEquals('qwe123', config('app.key'));
    }
}