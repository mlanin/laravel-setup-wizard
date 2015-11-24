# Laravel-Setup-Wizard
> Provide your Laravel project with handy setup wizard.

> :exclamation: Project is under development

## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, [Composer](https://getcomposer.org) and [Laravel](http://laravel.com) 5.1+ are required.

To get the latest version of Laravel-Setup-Wizard, simply install it via composer.

```bash
$ composer require lanin/laravel-setup-wizard
```

Once Laravel-Setup-Wizard is installed, you need to register the service provider. Open up `config/app.php` and add the following to the providers key.

```php
Lanin\Laravel\SetupWizard\SetupWizardServiceProvider::class,
```

## Usage

After installation project will receive the new Artisan command:

```bash
php artisan app:setup
```

This command gives your customers the easy wizard for initial setup of the your project. By default it has 6 steps:

1. Set new .env file from your .env.example
1. Create new DB & user from .env
1. Run migrations
1. Run seeds
1. Create first user
1. Optimize code

## Configuration



## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.