<?php namespace Lanin\Laravel\SetupWizard\Tests;

use Lanin\Laravel\SetupWizard\SetupWizardServiceProvider;

class SetupWizardServiceProviderTest extends TestCase
{
    /** @var SetupWizardServiceProvider */
    private $provider;

    public function setUp()
    {
        parent::setUp();
        $this->provider = $this->app->getProvider(SetupWizardServiceProvider::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->provider);
    }

    /** @test */
    public function it_can_get_provides_list()
    {
        $provided = $this->provider->provides();
        $defaults = ['setup-wizard.setup'];

        $this->assertCount(count($defaults), $provided);
        $this->assertEquals($defaults, $provided);
    }

    /** @test */
    public function it_cat_publish_config()
    {
        $config = $this->app['config'];

        $this->assertArrayHasKey('setup', $config);
        $this->assertArrayHasKey('setup.steps', $config);
    }
}