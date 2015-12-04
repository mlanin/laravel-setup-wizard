<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands\Steps;

use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\Seed;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class SeedTest extends TestCase
{

    /**
     * @return \Mockery\Mock
     */
    public function mockCommand()
    {
        $command = \Mockery::mock(Setup::class)->makePartial();
        $command->shouldReceive('confirm')->with('Everything is right?')->andReturn(true);

        return $command;
    }

    /** @test */
    public function it_has_prompt_text()
    {
        $step = new Seed($this->mockCommand());
        $this->assertEquals('Run database seeding?', $step->prompt());
    }

    /** @test */
    public function it_asks_for_seed_class()
    {
        $class = config('setup.seed.class');

        $command = $this->mockCommand();
        $command->shouldReceive('ask')->with('Seed to run', $class)->andReturn($class)->once();

        $step = new Seed($command);
        $this->assertEquals($class, $step->prepare());
    }

    /** @test */
    public function it_shows_preview()
    {
        $class = config('setup.seed.class');

        $command = $this->mockCommand();
        $command->shouldReceive('info')->with(
            'This command will be executed: <comment>php artisan db:seed --class=' . $class . ' --force --no-interaction</comment>'
        )->once();

        $step = new Seed($command);
        $this->assertNull($step->preview($class));
    }

    /** @test */
    public function it_runs_seed_artisan_command()
    {
        $class = config('setup.seed.class');

        \Artisan::shouldReceive('call')->with(
            'db:seed',
            ['--class' => $class, '--force' => true, '--no-interaction' => true]
        )->andReturn(0)->once();

        $step = new Seed($this->mockCommand());
        $this->assertTrue($step->finish($class));
    }
}