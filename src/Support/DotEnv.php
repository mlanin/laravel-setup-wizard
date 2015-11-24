<?php namespace Lanin\Laravel\SetupWizard\Support;

class DotEnv extends \Dotenv
{
    /**
     * @var array
     */
    public static $variables = [];

    /**
     * Overwrite to save variables to array.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public static function setEnvironmentVariable($name, $value = null)
    {
        list($name, $value) = static::normaliseEnvironmentVariable($name, $value);

        static::$variables[$name] = $value;
    }
}