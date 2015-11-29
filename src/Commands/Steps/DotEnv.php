<?php namespace Lanin\Laravel\SetupWizard\Commands\Steps;

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DotEnv extends AbstractStep
{
    const RANDOM = 'random';
    const SELECT = 'select';
    const INPUT  = 'input';

    protected $boostrappers = [
        'Illuminate\Foundation\Bootstrap\DetectEnvironment',
        'Lanin\Laravel\SetupWizard\Support\LoadConfiguration',
        'Illuminate\Foundation\Bootstrap\ConfigureLogging',
    ];

    /**
     * Return short command description.
     *
     * @return string
     */
    public function prompt()
    {
        return 'Do you want to ' . (file_exists(base_path('.env')) ? 'update' : 'create') . ' .env file?';
    }

    /**
     * Proceed step.
     *
     * @return array
     */
    protected function prepare()
    {
        $result = [];

        $file = config('setup.dot_env.default_file');

        if (file_exists(base_path('.env')) && $this->command->confirm('Existing .env file was found. Use it for defaults?', true))
        {
            $file = '.env';
        }

        \Lanin\Laravel\SetupWizard\Support\DotEnv::load(base_path(), $file);

        foreach (\Lanin\Laravel\SetupWizard\Support\DotEnv::$variables as $name => $default)
        {
            $options = config('setup.dot_env.variables.' . $name, ['type' => self::INPUT]);

            $result[$name] = $this->{'run' . $options['type']}($name, $options, $default);
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
        return $this->command->ask($this->generatePrompt($name, $options['prompt']), $default);
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
        return $this->command->choice($this->generatePrompt($name, $options['prompt']), $options['options'], array_search($default, $options['options']));
    }

    /**
     * Run random prompt.
     *
     * @param  string $name
     * @param  array $options
     * @param  string $default
     * @return string
     */
    protected function runRandom($name, array $options, $default = 'SomeRandomString')
    {
        $value = $this->command->ask($this->generatePrompt($name, $options['prompt']), $default);

        if ($value === 'SomeRandomString')
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
     * @param string $name
     * @param string $prompt
     * @return string
     */
    protected function generatePrompt($name, $prompt)
    {
        return $prompt . ' <comment>' . $name . '=?</comment>';
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

            /*
             * "Rebootstrap" Application to load new env variables to the config using native Application::bootstrapWith([]) method.
             *
             * In current Laravel implementation there is no method to reload Application config fully or Application itself.
             * Problem is that after reloading Config Repository it looses data from packages' config files
             * that are not currently published in /config directory. They are loaded only once after Application is bootstrapped.
             * And you can't force it to "rebootstrap" fully manually from outside (protected methods/properties).
             *
             * So the only way to "rebootstrap" it partly with custom Bootstrapper that uses existing Config Repository to
             * reload all /config files.
             */
            $this->command->getLaravel()->bootstrapWith($this->boostrappers);
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
