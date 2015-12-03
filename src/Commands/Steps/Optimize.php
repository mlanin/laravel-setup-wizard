<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

class Optimize extends AbstractStep
{
    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to optimize code?';
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
            'This command will be executed: <comment>php artisan optimize --force --no-interaction</comment>'
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
        return ! (bool) \Artisan::call('optimize', ['--force' => true, '--no-interaction' => true]);
    }
}