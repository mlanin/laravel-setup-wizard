<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Illuminate\Console\Command;

abstract class AbstractStep
{
    const REPEATS = 3;

    /**
     * @var int
     */
    protected $repeats = 0;

    /**
     * @var Command
     */
    protected $command;

    /**
     * Create a new Step.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Return command prompt text.
     *
     * @return string
     */
    abstract public function prompt();

    /**
     * Run step.
     *
     * @param  bool $pretend
     * @return mixed
     */
    public function run($pretend = false)
    {
        $results = $this->prepare();

        $this->preview($results);

        if ($this->command->confirm('Everything is right?'))
        {
            $return = ( ! $pretend) ? $this->finish($results) : true;
        }
        else
        {
            $return = ($this->repeats <= self::REPEATS) ?  $this->repeat() : false;
        }

        return $return;
    }

    /**
     * Repeat step.
     *
     * @return bool
     */
    public function repeat()
    {
        if ($this->command->confirm('Do you want to repeat step?'))
        {
            $this->repeats++;

            return $this->run();
        }

        return false;
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    abstract protected function prepare();

    /**
     * Preview results.
     *
     * @param  mixed $results
     * @return void
     */
    abstract public function preview($results);

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    abstract public function finish($results);
}