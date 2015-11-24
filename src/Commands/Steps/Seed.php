<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

class Seed extends AbstractStep
{
    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Run database seeding?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    protected function prepare()
    {
        return $this->command->ask('Seed to run', 'DatabaseSeeder');
    }

    /**
     * Preview results.
     *
     * @param  mixed $results
     * @return void
     */
    public function preview($results)
    {
        $this->command->info('We are about to run <comment>php artisan db:seed --class=' . $results . ' --force --no-interaction</comment>');
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        return ! (bool) \Artisan::call('db:seed', ['--class' => $results, '--force' => true, '--no-interaction' => true]);
    }
}