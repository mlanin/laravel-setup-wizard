<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Symfony\Component\Process\Process;

class CreateDatabase extends AbstractStep
{
    const USE_ENV_USER   = 'env';
    const USE_OTHER_USER = 'other';

    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to create database?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    protected function prepare()
    {
        $source = $this->command->choice(
            'Do you want to use user from .env or provide another one?',
            [self::USE_ENV_USER, self::USE_OTHER_USER],
            0
        );

        $return = [
            'commands' => [],
            'source'   => $source,
        ];

        $this->getEnvDatabase($return);

        switch ($source)
        {
            case self::USE_OTHER_USER:
                $this->getOtherUser($return);
                break;

            case self::USE_ENV_USER:
            default:
                $this->getEnvUser($return);
                break;
        }

        $this->generateSqlCommands($return);

        return $return;
    }

    /**
     * Get database config.
     *
     * @param array $return
     */
    protected function getEnvDatabase(array &$return)
    {
        $return['host']      = env('DB_HOST');
        $return['database']  = env('DB_DATABASE');
        $return['localhost'] = 'localhost';

        if ( ! in_array($return['host'], ['localhost', '127.0.0.1']))
        {
            $return['localhost'] = $this->command->ask('Database is on the other server. Provide local IP/hostname.');
        }
    }

    /**
     * Get user from environment.
     *
     * @param array $return
     */
    protected function getEnvUser(array &$return)
    {
        $return['username'] = env('DB_USERNAME');
        $return['password'] = env('DB_PASSWORD');
    }

    /**
     * Ask info about 'root' user.
     *
     * @param array $return
     */
    protected function getOtherUser(array &$return)
    {
        $return['username'] = $this->command->ask(
            'Provide user\'s login with <comment>CREATE DATABASE</comment> grants',
            'root'
        );
        $return['password'] = $this->command->secret('Password');
    }

    /**
     * Generate SQL commands.
     *
     * @param array $return
     */
    private function generateSqlCommands(array &$return)
    {
        $return['commands'] = [];

        $return['commands'][] = "CREATE DATABASE IF NOT EXISTS {$return['database']};";
        $return['commands'][] = sprintf(
            "GRANT ALL PRIVILEGES ON %s.* TO %s@%s IDENTIFIED BY '%s';",
            $return['database'],
            env('DB_USERNAME'),
            $return['localhost'],
            env('DB_PASSWORD')
        );
    }

    /**
     * Generate terminal command.
     *
     * @param  array $results
     * @param  bool $full
     * @return string
     */
    protected function prepareCommand($results, $full = false)
    {
        return sprintf(
            "mysql -u\"%s\" -p\"%s\" -h\"%s\" -e\"%s\"",
            $results['username'],
            $full ? $results['password'] : '******',
            $results['host'],
            join(' ', $results['commands'])
        );
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
            "This command will be executed: <comment>" . $this->prepareCommand($results) . "</comment>"
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
        $process = new Process($this->prepareCommand($results, true));

        $process->run(
            function ($type, $output) use (&$result)
            {
                $this->command->line($output);
            }
        );

        return ! (bool) $process->getExitCode();
    }
}