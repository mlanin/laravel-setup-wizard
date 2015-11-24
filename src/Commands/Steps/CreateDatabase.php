<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Symfony\Component\Process\Process;

class CreateDatabase extends AbstractStep
{
    const USE_ENV_USER = 'env';
    const SET_NEW_USER = 'new';

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
            'Do you want to use user from .env or provide new one?',
            [self::USE_ENV_USER, self::SET_NEW_USER],
            0
        );

        $return = [
            'commands' => [],
            'source'   => $source,
        ];

        $this->getEnvDatabase($return);

        switch ($source)
        {
            case self::SET_NEW_USER:
                $this->getNewUser($return);
                $this->createUserPrompt($return);
                break;

            case self::USE_ENV_USER:
            default:
                $this->getEnvUser($return);
                break;
        }

        return $return;
    }

    /**
     * @param array $return
     */
    protected function getEnvDatabase(array &$return)
    {
        $return['host'] = env('DB_HOST');
        $return['database'] = env('DB_DATABASE');

        $return['commands'][] = "CREATE DATABASE IF NOT EXISTS {$return['database']};";
    }

    /**
     * @param array $return
     */
    protected function getEnvUser(array &$return)
    {
        $return['username'] = env('DB_USERNAME');
        $return['password'] = env('DB_PASSWORD');
    }

    /**
     * @param array $return
     */
    protected function getNewUser(array &$return)
    {
        $return['username'] = $this->command->ask('Provide user with <comment>CREATE DATABASE</comment> grants', 'root');
        $return['password'] = $this->command->secret('Password');
    }

    /**
     * @param array $return
     */
    protected function createUserPrompt(array &$return)
    {
        if ($this->command->confirm('Do you want to create user from .env?'))
        {
            $return['commands'][] = sprintf(
                "GRANT ALL PRIVILEGES ON %s.* TO %s@%s IDENTIFIED BY '%s';",
                $return['database'],
                env('DB_USERNAME'),
                gethostname(),
                env('DB_PASSWORD')
            );
        }
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
        $this->command->info("We are about to run <comment>" . $this->prepareCommand($results) . "</comment>");
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

        $process->run(function ($type, $output) use (&$result) {
            $this->command->line($output);
        });

        return ! (bool) $process->getExitCode();
    }
}