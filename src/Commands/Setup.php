<?php namespace Lanin\Laravel\SetupWizard\Commands;

use Illuminate\Console\Command;
use Lanin\Laravel\SetupWizard\Commands\Steps\AbstractStep;

class Setup extends Command
{
    const VERSION = '0.1';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup
                            {step? : Step to run}
                            {--P|pretend : Pretend to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the project.';

    /**
     * Installation steps.
     *
     * @var array
     */
    protected $steps = [];

    /**
     * @inheritDoc
     */
    public function getHelp()
    {
        $steps = config('setup.steps');

        $help = $this->description . ' Available steps:' . PHP_EOL;

        foreach ($steps as $alias => $class)
        {
            $help .= '  - <comment>' . $alias . '</comment>'. PHP_EOL;
        }

        return $help;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(sprintf('%s (v%s)', config('setup.title'), self::VERSION));

        $this->steps = config('setup.steps');

        $step = $this->argument('step');
        $pretend = (bool) $this->option('pretend');

        $return = empty($step) ? $this->runAllSteps($pretend) : $this->runStep($step, $pretend);

        $this->info('Setup finished.');

        return $return;
    }

    /**
     * Run all steps one by one.
     *
     * @param  bool $pretend
     * @return bool
     */
    protected function runAllSteps($pretend = false)
    {
        $return = 0;
        foreach (array_keys($this->steps) as $step)
        {
            $return += (int) $this->runStep($step, $pretend);
        }

        return ! (bool) $return;
    }

    /**
     * Run installation step
     *
     * @param  string $step
     * @param  bool $pretend
     * @return bool
     */
    protected function runStep($step, $pretend = false)
    {
        if (isset($this->steps[$step]) && class_exists($this->steps[$step]))
        {
            $class = $this->steps[$step];
            $step  = new $class($this);

            if ($this->confirm($step->prompt(), true))
            {
                return $step->run($pretend);
            }
        }
        else
        {
            $this->error("Step '{$step}' doesn't exist.");
        }

        return false;
    }
}
