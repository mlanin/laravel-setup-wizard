<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands\Steps;

use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\AbstractStep;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class AbstractStepTest extends TestCase
{
    /** @var Setup */
    private $setup;

    /** @var AbstractStep */
    private $step;

    public function setUp()
    {
        parent::setUp();
        $this->setup = $this->mockCommand();
        $this->step  = new TestStep($this->setup);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->setup, $this->step);
    }

    public function mockCommand()
    {
        $command = \Mockery::mock(Setup::class)->makePartial();
        $command->shouldReceive('confirm')->with('Do you want to run test step?', true)->andReturn(true);
        $command->shouldReceive('confirm')->with('Everything is right?')->andReturn(true);
        $command->shouldReceive('confirm')->with('Do you want to repeat step?')->andReturn(true);

        return $command;
    }

    /** @test */
    public function it_can_be_runned()
    {
        $this->assertTrue($this->step->run());
    }

    /** @test */
    public function it_will_ask_for_repeat_if_user_cancels()
    {
        $command = \Mockery::mock(Setup::class)->makePartial();
        $command->shouldReceive('confirm')->with('Everything is right?')->andReturn(false)->once();
        $command->shouldReceive('confirm')->with('Do you want to repeat step?')->andReturn(false)->once();

        $step = new TestStep($command);
        $this->assertFalse($step->run());
    }

    /** @test */
    public function it_will_be_repeated_on_finish_error()
    {
        $command = \Mockery::mock(Setup::class)->makePartial();
        $command->shouldReceive('confirm')->with('Everything is right?')->andReturn(true)->once();
        $command->shouldReceive('confirm')->with('Do you want to repeat step?')->andReturn(false)->once();

        $step = \Mockery::mock(TestStep::class, [$command])->makePartial();
        $step->shouldReceive('finish')->andReturn(false)->once();

        $this->assertFalse($step->run());
    }

    /** @test */
    public function it_can_be_repeated_three_times()
    {
        $this->assertTrue($this->step->repeat());
        $this->assertTrue($this->step->repeat());
        $this->assertTrue($this->step->repeat());
        $this->assertFalse($this->step->repeat());
    }

    /** @test */
    public function it_can_be_runned_through_the_command()
    {
        $property = $this->getPublicProperty($this->setup, 'steps');
        $steps = $property->getValue($this->setup);
        $steps['test'] = TestStep::class;
        $property->setValue($this->setup, $steps);

        $runStep = $this->getPublicMethod($this->setup, 'runStep');
        $this->assertTrue($runStep->invoke($this->setup, 'test'));
    }

    /** @test */
    public function it_can_transform_array_to_table_usage()
    {
        $step = new TestStep($this->mockCommand());
        $arrayToTable = $this->getPublicMethod($step, 'arrayToTable');

        $this->assertEquals(
            [
                ['foo', 'bar'],
                ['qwe', 123],
            ],
            $arrayToTable->invoke($step, ['foo' => 'bar', 'qwe' => 123])
        );
    }
}

class TestStep extends AbstractStep
{

    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to run test step?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    public function prepare()
    {
        return null;
    }

    /**
     * Preview results.
     *
     * @param  mixed $results
     * @return void
     */
    public function preview($results)
    {
        return null;
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        return true;
    }
}