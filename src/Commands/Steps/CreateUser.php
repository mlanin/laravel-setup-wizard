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

        $result['__table'] = $this->getTable(config('setup.create_user.table'));

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
        $this->command->info('I will insert this values into <comment>' . $results['__table'] . '</comment> table');
        $this->command->table(['column', 'value'], $this->arrayToTable($results));
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        $table = $results['__table'];
        unset($results['__table']);

        return \DB::table($table)->insert($results);
    }

    /**
     * Get users table name.
     *
     * @param  string $table
     * @return string
     */
    protected function getTable($table = '')
    {
        if (empty($table))
        {
            switch (config('auth.driver'))
            {
                case 'eloquent':
                    $table = $this->getTableByModelClass(config('auth.model'));
                    break;
                case 'database':
                default:
                    $table = config('auth.table');
                    break;
            }
        }

        return $table;
    }

    /**
     * Resolve user's model and get associated table.
     *
     * @param  string $model
     * @return string
     */
    protected function getTableByModelClass($model)
    {
        $model = new $model();
        $table = $model->getTable();

        return $table;
    }
}