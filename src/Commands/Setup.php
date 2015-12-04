<?php namespace Lanin\Laravel\SetupWizard\Commands;

use Illuminate\Console\Command;
use Lanin\Laravel\SetupWizard\Commands\Steps\AbstractStep;

class Setup extends Command
{
    const VERSION = '0.1.2';

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
    public $steps = [];

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->steps = config('setup.steps');
    }

    /**
     * @inheritDoc
     */
    public function getHelp()
    {
        $help = $this->description . ' Available steps:' . PHP_EOL;

        foreach ($this->steps as $alias => $class)
        {
            $help .= '  - <comment>' . $alias . '</comment>' . PHP_EOL;
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

        $pretend = (bool) $this->option('pretend');
        $steps   = (array) $this->argument('step');
        $return  = $this->runSteps($steps, $pretend);

        $this->info('Setup finished.');

        return $return;
    }

    /**
     * Run all steps one by one.
     *
     * @param  array $steps
     * @param  bool $pretend
     * @return bool
     */
    protected function runSteps($steps = [], $pretend = false)
    {
        $return = true;
        $steps  = empty($steps) ? array_keys($this->steps) : $steps;

        foreach ($steps as $step)
        {
            if ($this->runStep($step, $pretend) === false)
            {
                $return = false;
            }
        }

        return $return;
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
        try
        {
            $step = $this->createStep($step);

            if ($this->confirm($step->prompt(), true))
            {
                return $step->run($pretend);
            }
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }

        return false;
    }

    /**
     * Instantiate step class.
     *
     * @param string $step
     * @return AbstractStep
     * @throws \Exception
     */
    protected function createStep($step)
    {
        if ( ! $this->stepExist($step))
        {
            throw new \Exception("Step <comment>{$step}</comment> doesn't exist.");
        }

        $class = $this->steps[$step];
        $step  = new $class($this);

        if ( ! ($step instanceof AbstractStep))
        {
            throw new \Exception("Step class <comment>{$class}</comment> should be an instance of AbstractStep.");
        }

        return $step;
    }

    /**
     * Check if step exists.
     *
     * @param  string $step
     * @return bool
     */
    protected function stepExist($step)
    {
        return isset($this->steps[$step]) && class_exists($this->steps[$step]);
    }

}
