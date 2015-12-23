<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Illuminate\Console\Command;

abstract class AbstractStep
{
    const REPEATS = 3;

    /**
     * @var int
     */
    protected $repeats = 1;

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
            return $pretend ?: $this->finish($results);
        }

        return false;
    }

    /**
     * Repeat step.
     *
     * @return bool
     */
    public function repeat()
    {
        $repeatCountExceeded = $this->repeats > static::REPEATS;
        $wantToRepeat = $this->command->confirm('Do you want to repeat step?');

        if ($repeatCountExceeded || ! $wantToRepeat)
        {
            return false;
        }

        $this->repeats++;

        return true;
    }

    /**
     * Transform associative array to use in command->table() method.
     *
     * @param  array $array
     * @return array
     */
    protected function arrayToTable(array $array)
    {
        list($keys, $values) = array_divide($array);

        return collect($keys)->zip(collect($values))->toArray();
    }

    /**
     * Return command prompt text.
     *
     * @return string
     */
    abstract public function prompt();

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    abstract public function prepare();

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
