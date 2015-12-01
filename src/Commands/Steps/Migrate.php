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
        $this->command->info(
            'This command will be executed: <comment>php artisan migrate:refresh --force --no-interaction</comment>'
        );
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        return ! (bool) \Artisan::call('migrate:refresh', ['--force' => true, '--no-interaction' => true]);
    }
}