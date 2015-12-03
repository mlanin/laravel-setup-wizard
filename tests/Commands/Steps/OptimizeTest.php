<?php namespace Lanin\Laravel\SetupWizard\Tests\Commands\Steps;

use Lanin\Laravel\SetupWizard\Commands\Setup;
use Lanin\Laravel\SetupWizard\Commands\Steps\Optimize;
use Lanin\Laravel\SetupWizard\Tests\TestCase;

class OptimizeTest extends TestCase
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
        $step = new Optimize($this->mockCommand());
        $this->assertEquals('Do you want to optimize code?', $step->prompt());
    }

    /** @test */
    public function it_shows_preview()
    {
        $command = $this->mockCommand();
        $command->shouldReceive('info')->with(
            'This command will be executed: <comment>php artisan optimize --force --no-interaction</comment>'
        )->once();

        $step = new Optimize($command);
        $this->assertNull($step->preview(null));
    }

    /** @test */
    public function it_runs_seed_artisan_command()
    {
        $artisan = \Mockery::mock($this->app['Illuminate\Contracts\Console\Kernel']);
        $artisan->shouldReceive('call')->with('optimize', ['--force' => true, '--no-interaction' => true])->andReturn(0)->once();

        $this->app['Illuminate\Contracts\Console\Kernel'] = $artisan;

        $step = new Optimize($this->mockCommand());
        $this->assertTrue($step->finish(null));
    }
}