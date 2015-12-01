<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

class CreateUser extends AbstractStep
{
    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Create new user?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    public function prepare()
    {
        $result = [];
        foreach (config('setup.create_user.fields') as $column => $title)
        {
            $result[$column] = $this->command->ask($title);

            if ($column == config('setup.create_user.password_field'))
            {
                $result[$column] = bcrypt($result[$column]);
            }
        }

        return $result;
    }

    /**
     * Preview results.
     *
     * @param  mixed $results
     * @return void
     */
    public function preview($results)
    {
        list($keys, $values) = array_divide($results);

        $this->command->info('I will insert this values into <comment>' . $this->getTable() . '</comment> table');
        $this->command->table(['column', 'value'], collect($keys)->zip(collect($values))->toArray());
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        $table = $this->getTable();

        return \DB::table($table)->insert($results);
    }

    /**
     * Get users table name.
     *
     * @return mixed
     */
    protected function getTable()
    {
        $table = config('setup.create_user.table');

        if (empty($table))
        {
            switch (config('auth.driver'))
            {
                case 'eloquent':
                    $model = config('auth.model');
                    $model = new $model();
                    $table = $model->getTable();
                    break;
                case 'database':
                default:
                    $table = config('auth.table');
                    break;
            }

            return $table;
        }

        return $table;
    }
}