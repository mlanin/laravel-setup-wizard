<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

class Migrate extends AbstractStep
{
    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Run database migration?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    protected function prepare()
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
        $this->command->info('We are about to run <comment>php artisan migrate --force --no-interaction</comment>');
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        return ! (bool) \Artisan::call('migrate', ['--force', '--no-interaction']);
    }
}