<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

class NewUser extends AbstractStep
{
    public static $table    = '';
    public static $password = 'password';
    public static $fields   = [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
    ];

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
    protected function prepare()
    {
        $result = [];
        foreach (static::$fields as $column => $title)
        {
            $result[$column] = $this->command->ask($title);

            if ($column == static::$password)
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
        return;
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        $table = static::$table;

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
        }

        return \DB::table($table)->insert($results);
    }
}