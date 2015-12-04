<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Symfony\Component\Process\Process;

class CreateMysqlDatabase extends AbstractStep
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @inheritDoc
     */
    public function __construct(Command $command)
    {
        parent::__construct($command);

        $this->connection = \DB::connection();
    }


    /**
     * Return command prompt text.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to create MySQL database?';
    }

    /**
     * Prepare step data.
     *
     * @return mixed
     */
    public function prepare()
    {
        $return = [];

        $this->getDatabaseConfigs($return);

        if ($this->command->confirm('Do you want to provide an other user to connect to DB?', false))
        {
            $this->getOtherConnectionUser($return);
        }

        $this->generateSqlCommands($return);

        return $return;
    }

    /**
     * Get database config.
     *
     * @param array $return
     */
    protected function getDatabaseConfigs(array &$return)
    {
        $return['localhost'] = 'localhost';

        $return['host']     = $this->connection->getConfig('host');
        $return['database'] = $this->connection->getConfig('database');
        $return['username'] = $this->connection->getConfig('username');
        $return['password'] = $this->connection->getConfig('password');
        $return['connection_username'] = $return['username'];
        $return['connection_password'] = $return['password'];

        if ( ! in_array($return['host'], ['localhost', '127.0.0.1']))
        {
            $return['localhost'] = $this->command->ask('Database is on the other server. Provide local IP/hostname.');
        }
    }

    /**
     * Ask info about 'root' user.
     *
     * @param array $return
     */
    protected function getOtherConnectionUser(array &$return)
    {
        $return['connection_username'] = $this->command->ask(
            'Provide user\'s login with <comment>CREATE DATABASE</comment> grants',
            'root'
        );
        $return['connection_password'] = $this->command->secret('Password');
    }

    /**
     * Generate SQL commands.
     *
     * @param array $return
     */
    protected function generateSqlCommands(array &$return)
    {
        $return['commands']   = [];
        $return['commands'][] = "CREATE DATABASE IF NOT EXISTS {$return['database']};";
        $return['commands'][] = sprintf(
            "GRANT ALL PRIVILEGES ON %s.* TO %s@%s IDENTIFIED BY '%s';",
            $return['database'],
            $return['username'],
            $return['localhost'],
            $return['password']
        );
    }

    /**
     * Generate terminal command.
     *
     * @param  array $results
     * @param  bool $full
     * @return string
     */
    protected function generateConsoleCommand($results, $full = false)
    {
        return sprintf(
            "mysql -u\"%s\" -p\"%s\" -h\"%s\" -e\"%s\"",
            $results['connection_username'],
            $full ? $results['connection_password'] : '******',
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
            "This command will be executed: <comment>" . $this->generateConsoleCommand($results) . "</comment>"
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
        $process = new Process($this->generateConsoleCommand($results, true));

        $process->run(function ($type, $output)
        {
            $this->command->line($output);
        });

        return ! (bool) $process->getExitCode();
    }
}