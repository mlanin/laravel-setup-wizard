<?php

use Lanin\Laravel\SetupWizard\Commands\Steps\DotEnv;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateDatabase;
use Lanin\Laravel\SetupWizard\Commands\Steps\Migrate;
use Lanin\Laravel\SetupWizard\Commands\Steps\Seed;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateUser;
use Lanin\Laravel\SetupWizard\Commands\Steps\Optimize;

return [
    'title' => 'Laravel Setup Wizard',
    'steps' => [
        'dot_env'           => DotEnv::class,
        'create_database'   => CreateDatabase::class,
        'migrate'           => Migrate::class,
        'seed'              => Seed::class,
        'create_user'       => CreateUser::class,
        'optimize'          => Optimize::class,
    ],
    'create_user' => [
        'table'    => '',
        'password_field' => 'password',
        'fields'   => [
            'name'     => 'Name',
            'email'    => 'Email',
            'password' => 'Password',
        ]
    ],
    'seed' => [
        'class' => 'DatabaseSeeder',
    ],
    'dot_env' => [
        'default_file' => '.env.example',
        'variables' => [
            'APP_ENV' => [
                'type' => DotEnv::SELECT,
                'options' => ['local', 'production'],
            ],
            'APP_DEBUG' => [
                'type' => DotEnv::SELECT,
                'options' => ['true', 'false'],
            ],
            'APP_KEY' => [
                'type' => DotEnv::RANDOM,
            ],

            'DB_CONNECTION' => [
                'type' => DotEnv::SELECT,
                'options' => ['mysql', 'sqlite', 'pgsql', 'sqlsrv'],
            ],
            'DB_HOST' => [
                'type' => DotEnv::INPUT,
            ],
            'DB_DATABASE' => [
                'type' => DotEnv::INPUT,
            ],
            'DB_USERNAME' => [
                'type' => DotEnv::INPUT,
            ],
            'DB_PASSWORD' => [
                'type' => DotEnv::INPUT,
            ],

            'CACHE_DRIVER' => [
                'type' => DotEnv::SELECT,
                'options' => ['apc', 'array', 'database', 'file', 'memcached', 'redis'],
            ],
            'SESSION_DRIVER' => [
                'type' => DotEnv::SELECT,
                'options' => ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'array'],
            ],
            'QUEUE_DRIVER' => [
                'type' => DotEnv::SELECT,
                'options' => ['null', 'sync', 'database', 'beanstalkd', 'sqs', 'iron', 'redis'],
            ],

            'MAIL_DRIVER' => [
                'type' => DotEnv::SELECT,
                'options' => ['smtp', 'mail', 'sendmail', 'mailgun', 'mandrill', 'ses', 'log'],
            ],
            'MAIL_HOST' => [
                'type' => DotEnv::INPUT,
            ],
            'MAIL_PORT' => [
                'type' => DotEnv::INPUT,
            ],
            'MAIL_USERNAME' => [
                'type' => DotEnv::INPUT,
            ],
            'MAIL_PASSWORD' => [
                'type' => DotEnv::INPUT,
            ],
            'MAIL_ENCRYPTION' => [
                'type' => DotEnv::INPUT,
            ],
        ]
    ]

];