# Laravel-Setup-Wizard [badge_build]:https://img.shields.io/travis/mlanin/laravel-setup-wizard.svg?style=flat-square [badge_coverage]:https://img.shields.io/scrutinizer/coverage/g/mlanin/laravel-setup-wizard.svg?style=flat-square
> Provide your Laravel project with handy setup wizard.

Guide your customer through all steps he need to perform for a proper setup of your project.

![help](http://lanin.me/images/setup.png)

## Installation

[PHP](https://php.net) 5.5.9+ or [HHVM](http://hhvm.com) 3.3+, [Composer](https://getcomposer.org) and [Laravel](http://laravel.com) 5.1+ are required.

To get the latest version of Laravel-Setup-Wizard, simply install it via composer.

```bash
$ composer require "lanin/laravel-setup-wizard:0.1.*"
```

Once Laravel-Setup-Wizard is installed, you need to register the service provider. Open up `config/app.php` and add the following to the providers key.

```php
Lanin\Laravel\SetupWizard\SetupWizardServiceProvider::class,
```

## Usage

After installation project will receive the new Artisan command:

```bash
$ php artisan app:setup
```

This command gives your customers the easy wizard for initial setup of the your project. By default it has 6 steps:

1. Set new `.env` file from your `.env.example` or update existing
1. Create new DB & user from `.env`
1. Run migrations
1. Run seeds
1. Create first user
1. Optimize code

Also they can run single step manually by adding it's alias as an argument:

```bash
$ php artisan app:setup create_user
```

For additional help use:

```bash
$ php artisan help app:setup
```

## Configuration

All defaults are stored in `setup.php` config file. You can publish it in your app's `config` folder using artisan command:

```bash
$ php artisan vendor:publish
```

## Extending

You can create your own installations steps specifying them in your `setup.php` config.

First you should create new step class that will extend `Lanin\Laravel\SetupWizard\Commands\Steps\AbstractStep`.
It has three abstract methods that you have to create:

```php
/**
 * Return command prompt text.
 *
 * @return string
 */
abstract public function prompt();
```

This method has to return prompt text that will be shown to the user before step is executed.

```php
/**
 * Prepare step data.
 *
 * @return mixed
 */
abstract protected function prepare();
```

This method can be used to collect all needed data for the step to execute.
For example ask user for extra data or credentials, etc. All this data should be returned for further execution.

```php
/**
 * Preview results.
 *
 * @param  mixed $results
 * @return void
 */
abstract public function preview($results);
```

Preview method used to show user info about what particular commands will be executed. 
It is used to make user sure that everything will be ok.

```php
/**
 * Finish step.
 *
 * @param  mixed $results
 * @return bool
 */
abstract public function finish($results);
```

The last but the most important step is right for the step execution. 
Should return boolean true if everything was ok and false if there was an error.

After everything is done, add your step to the `setup.steps` array in your `setup.php` file where key should be a step's alias and value is a fully resolved class name.

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.