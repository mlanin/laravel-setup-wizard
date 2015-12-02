<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands;

use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\AbstractStep;
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
    public function it_shows_help_with_all_steps()
    {
        $steps = config('setup.steps');
        $help  = $this->setup->getHelp();

        foreach (array_keys($steps) as $step)
        {
            $this->assertContains($step, $help);
        }
    }

    /** @test */
    public function it_checks_if_step_exists_in_config()
    {
        $stepExist = $this->getPublicMethod($this->setup, 'stepExist');

        $this->assertFalse($stepExist->invoke($this->setup, 'foo'));
        $this->assertTrue($stepExist->invoke($this->setup, key(config('setup.steps'))));
    }

    /** @test */
    public function it_throws_exception_if_try_to_instantiate_unknown_step()
    {
        $this->setExpectedException(\Exception::class, "Step <comment>foo</comment> doesn't exist.");

        $createStep = $this->getPublicMethod($this->setup, 'createStep');
        $createStep->invoke($this->setup, 'foo');
    }

    /** @test */
    public function it_throws_exception_if_try_to_instantiate_not_abstract_step_class()
    {
        $this->setExpectedException(\Exception::class, "Step class <comment>stdClass</comment> should be an instance of AbstractStep.");

        $property = $this->getPublicProperty($this->setup, 'steps');
        $steps = $property->getValue($this->setup);
        $steps['foo'] = \stdClass::class;
        $property->setValue($this->setup, $steps);

        $createStep = $this->getPublicMethod($this->setup, 'createStep');
        $createStep->invoke($this->setup, 'foo');
    }

    /** @test */
    public function it_can_create_all_default_steps_by_alias()
    {
        $createStep = $this->getPublicMethod($this->setup, 'createStep');

        foreach (array_keys(config('setup.steps')) as $step)
        {
            $step = $createStep->invoke($this->setup, $step);
            $this->assertInstanceOf(AbstractStep::class, $step);
        }
    }

    /** @test */
    public function it_shows_error_on_unknown_step()
    {
        $runStep = $this->getPublicMethod($this->setup, 'runStep');

        $setup = \Mockery::mock($this->setup);
        $setup->shouldReceive('error')->with("Step <comment>foo</comment> doesn't exist.");

        $this->assertFalse($runStep->invoke($setup, 'foo'));
    }

    /** @test */
    public function it_can_run_all_steps()
    {
        $runAllSteps = $this->getPublicMethod($this->setup, 'runAllSteps');

        $setup = \Mockery::mock($this->setup)->makePartial()->shouldAllowMockingProtectedMethods();

        $steps = config('setup.steps');

        $property = $this->getPublicProperty($setup, 'steps');
        $property->setValue($setup, $steps);

        foreach (array_keys($steps) as $step)
        {
            $setup->shouldReceive('runStep')->with($step, false)->andReturn(true);
        }

        $this->assertTrue($runAllSteps->invoke($setup));
    }

    /** @test */
    public function it_can_run_all_steps_and_return_false()
    {
        $runAllSteps = $this->getPublicMethod($this->setup, 'runAllSteps');

        $setup = \Mockery::mock($this->setup)->makePartial()->shouldAllowMockingProtectedMethods();

        $steps = config('setup.steps');

        $property = $this->getPublicProperty($setup, 'steps');
        $property->setValue($setup, $steps);

        foreach (array_keys($steps) as $step)
        {
            $setup->shouldReceive('runStep')->with($step, false)->andReturn(false);
        }

        $this->assertFalse($runAllSteps->invoke($setup));
    }

    /** @test */
    public function it_can_be_handled_without_defined_step()
    {
        $setup = \Mockery::mock(Setup::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $setup->shouldReceive('info', 'error')->withAnyArgs()->zeroOrMoreTimes();
        $setup->shouldReceive('argument')->andReturn(null);
        $setup->shouldReceive('option')->andReturn(null);
        $setup->shouldReceive('runAllSteps')->andReturn(true);

        $this->assertTrue($setup->handle());
    }

    /** @test */
    public function it_can_be_handled_with_defined_step()
    {
        $setup = \Mockery::mock(Setup::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $setup->shouldReceive('info', 'error')->withAnyArgs()->zeroOrMoreTimes();
        $setup->shouldReceive('argument')->andReturn(key(config('setup.steps')));
        $setup->shouldReceive('option')->andReturn(null);
        $setup->shouldReceive('runStep')->with(key(config('setup.steps')), false)->andReturn(true);

        $this->assertTrue($setup->handle());
    }
}

