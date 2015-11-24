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
    protected function prepare()
    {
        // TODO: Implement prepare() method.
    }

    /**
     * Preview results.
     *
     * @param  mixed $results
     * @return void
     */
    public function preview($results)
    {
        $this->command->info('We are about to run <comment>php artisan optimize --force --no-interaction</comment>');
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        return ! (bool) \Artisan::call('optimize', ['--force', '--no-interaction']);
    }
}