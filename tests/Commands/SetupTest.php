<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands;

use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class SetupTest extends TestCase
{
    /** @var Setup */
    private $setup;

    public function setUp()
    {
        parent::setUp();
        $this->setup = $this->app['setup-wizard.setup'];
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->setup);
    }

    /** @test */
    public function it_shows_help()
    {

    }
}