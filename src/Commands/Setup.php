<?php namespace Lanin\Laravel\SetupWizard\Commands;

use Lanin\Laravel\SetupWizard\Commands\Steps\CreateDatabase;
use Lanin\Laravel\SetupWizard\Commands\Steps\DotEnv;
use Lanin\Laravel\SetupWizard\Commands\Steps\Migrate;
use Lanin\Laravel\SetupWizard\Commands\Steps\NewUser;
use Lanin\Laravel\SetupWizard\Commands\Steps\Optimize;
use Lanin\Laravel\SetupWizard\Commands\Steps\Seed;
use Illuminate\Console\Command;

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
    protected $description = 'Setup the project';

    /**
     * Installation steps.
     *
     * @var array
     */
    public static $steps = [
        '.env'              => DotEnv::class,
        'create_database'   => CreateDatabase::class,
        'migrate'           => Migrate::class,
        'seed'              => Seed::class,
        'create_user'       => NewUser::class,
        'optimize'          => Optimize::class,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Laravel Setup Wizard v' . self::VERSION);

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
        foreach (array_keys(static::$steps) as $step)
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
        if (isset(static::$steps[$step]) && class_exists(static::$steps[$step]))
        {
            $class = static::$steps[$step];
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
