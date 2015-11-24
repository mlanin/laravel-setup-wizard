<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Illuminate\Support\Str;

class DotEnv extends AbstractStep
{
    const RANDOM = 'random';
    const SELECT = 'select';
    const INPUT  = 'input';

    /**
     * @var string
     */
    public static $exampleEnv = '.env.example';

    /**
     * @var array
     */
    public static $defaults = [
        'APP_ENV' => [
            'type' => self::SELECT,
            'options' => ['local', 'production'],
        ],
        'APP_DEBUG' => [
            'type' => self::SELECT,
            'options' => ['true', 'false'],
        ],
        'APP_KEY' => [
            'type' => self::RANDOM,
        ],

        'DB_CONNECTION' => [
            'type' => self::SELECT,
            'options' => ['mysql', 'sqlite', 'pgsql', 'sqlsrv'],
        ],
        'DB_HOST' => [
            'type' => self::INPUT,
        ],
        'DB_DATABASE' => [
            'type' => self::INPUT,
        ],
        'DB_USERNAME' => [
            'type' => self::INPUT,
        ],
        'DB_PASSWORD' => [
            'type' => self::INPUT,
        ],

        'CACHE_DRIVER' => [
            'type' => self::SELECT,
            'options' => ['apc', 'array', 'database', 'file', 'memcached', 'redis'],
        ],
        'SESSION_DRIVER' => [
            'type' => self::SELECT,
            'options' => ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'array'],
        ],
        'QUEUE_DRIVER' => [
            'type' => self::SELECT,
            'options' => ['null', 'sync', 'database', 'beanstalkd', 'sqs', 'iron', 'redis'],
        ],

        'MAIL_DRIVER' => [
            'type' => self::SELECT,
            'options' => ['smtp', 'mail', 'sendmail', 'mailgun', 'mandrill', 'ses', 'log'],
        ],
        'MAIL_HOST' => [
            'type' => self::INPUT,
        ],
        'MAIL_PORT' => [
            'type' => self::INPUT,
        ],
        'MAIL_USERNAME' => [
            'type' => self::INPUT,
        ],
        'MAIL_PASSWORD' => [
            'type' => self::INPUT,
        ],
        'MAIL_ENCRYPTION' => [
            'type' => self::INPUT,
        ],
    ];


    /**
     * Return short command description.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to set .env file?';
    }

    /**
     * Proceed step.
     *
     * @return array
     */
    protected function prepare()
    {
        $result = [];

        $file = static::$exampleEnv;

        if (file_exists(base_path('.env')) && $this->command->confirm('We found existing .env file. Use it for default?', true))
        {
            $file = '.env';
        }

        \Lanin\Laravel\SetupWizard\Support\DotEnv::load(base_path(), $file);

        foreach (\Lanin\Laravel\SetupWizard\Support\DotEnv::$variables as $name => $default)
        {
            if (isset(static::$defaults[$name]))
            {
                $options = static::$defaults[$name];

                $result[$name] = $this->{'run' . $options['type']}($name, $options, $default);
            }
            else
            {
                $result[$name] = $this->runInput($name, [], $default);
            }
        }

        return $result;
    }

    /**
     * Run input prompt.
     *
     * @param  string $name
     * @param  array $options
     * @param  string|null $default
     * @return string
     */
    protected function runInput($name, array $options, $default = null)
    {
        return $this->command->ask($name, $default);
    }

    /**
     * Run select prompt.
     *
     * @param  string $name
     * @param  array $options
     * @param  string|null $default
     * @return string
     */
    protected function runSelect($name, array $options, $default = null)
    {
        return $this->command->choice($name, $options['options'], array_search($default, $options['options']));
    }

    /**
     * Run random prompt.
     *
     * @param  string $name
     * @param  array $options
     * @param  string $default
     * @return string
     */
    protected function runRandom($name, array $options, $default = 'random')
    {
        $value = $this->command->ask($name . ' (live empty for random string)', $default);

        if ($value === 'random')
        {
            $value = $this->getRandomKey(config('app.cipher'));
        }

        return $value;
    }

    /**
     * Generate a random key for the application.
     *
     * @param  string  $cipher
     * @return string
     */
    protected function getRandomKey($cipher)
    {
        if ($cipher === 'AES-128-CBC')
        {
            return Str::random(16);
        }

        return Str::random(32);
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

        $this->command->table(['Variable', 'Value'], collect($keys)->zip(collect($values))->toArray());
    }

    /**
     * Finish step.
     *
     * @param  mixed $results
     * @return bool
     */
    public function finish($results)
    {
        $return = $this->saveFile($results);

        if ($return)
        {
            $this->command->info('New .env file was saved');

            \Dotenv::load(base_path());
        }
        else
        {
            $this->command->error('Failed to save .env file. Check permissions please.');
        }

        return $return;
    }

    /**
     * Save .env file.
     *
     * @param $results
     * @return bool
     */
    protected function saveFile($results)
    {
        $file = fopen(base_path('.env'), 'w+');
        foreach ($results as $variable => $value)
        {
            fwrite($file, $variable . '=' . $value . PHP_EOL);
        }

        return fclose($file);
    }
}