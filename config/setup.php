<?php

use Lanin\Laravel\SetupWizard\Commands\Steps\DotEnv;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateDatabase;
use Lanin\Laravel\SetupWizard\Commands\Steps\Migrate;
use Lanin\Laravel\SetupWizard\Commands\Steps\Seed;
use Lanin\Laravel\SetupWizard\Commands\Steps\CreateUser;
use Lanin\Laravel\SetupWizard\Commands\Steps\Optimize;

return [
    // Setup title. Shown in the beginning of setup.
    'title' => 'Laravel Setup Wizard',

    // Steps to run. Will be run in the order as they defined in this array.
    'steps' => [
        'dot_env'           => DotEnv::class,
        'create_database'   => CreateDatabase::class,
        'migrate'           => Migrate::class,
        'seed'              => Seed::class,
        'create_user'       => CreateUser::class,
        'optimize'          => Optimize::class,
    ],

    // Options for create_user step.
    'create_user' => [
        // Table to insert.
        'table'    => '',
        // Password field name.
        'password_field' => 'password',
        // Columns and their titles to use.
        'fields'   => [
            'name'     => 'Name',
            'email'    => 'Email',
            'password' => 'Password',
        ]
    ],

    // Options for seed step.
    'seed' => [
        // Default class to use.
        'class' => 'DatabaseSeeder',
    ],

    // Options for dot_env step.
    'dot_env' => [
        // File for defaults.
        'default_file' => '.env.example',
        // .env variables with their options.
        // Variable input types:
        //  - DotEnv::INPUT ask to input a value.
        //  - DotEnv::SELECT ask to choose a value. Should contain array of options.
        //  - DotEnv::RANDOM ask if user wants to generate a random value.
        'variables' => [
            'APP_ENV' => [
                'prompt' => 'What application environment to use?',
                'type' => DotEnv::SELECT,
                'options' => ['local', 'production'],
            ],
            'APP_DEBUG' => [
                'prompt' => 'Enable debug mode?',
                'type' => DotEnv::SELECT,
                'options' => ['true', 'false'],
            ],
            'APP_KEY' => [
                'prompt' => 'Application unique key. For initial setup better to leave random.',
                'type' => DotEnv::RANDOM,
            ],

            'DB_CONNECTION' => [
                'prompt' => 'What database connection to use?',
                'type' => DotEnv::SELECT,
                'options' => ['mysql', 'sqlite', 'pgsql', 'sqlsrv'],
            ],
            'DB_HOST' => [
                'prompt' => 'Set database host.',
                'type' => DotEnv::INPUT,
            ],
            'DB_DATABASE' => [
                'prompt' => 'Set database name.',
                'type' => DotEnv::INPUT,
            ],
            'DB_USERNAME' => [
                'prompt' => 'Provide username to connect to the database.',
                'type' => DotEnv::INPUT,
            ],
            'DB_PASSWORD' => [
                'prompt' => 'Provide password to use to connect to the database.',
                'type' => DotEnv::INPUT,
            ],

            'CACHE_DRIVER' => [
                'prompt' => 'What cache driver do you want to use?',
                'type' => DotEnv::SELECT,
                'options' => ['apc', 'array', 'database', 'file', 'memcached', 'redis'],
            ],
            'SESSION_DRIVER' => [
                'prompt' => 'What session driver do you want to use?',
                'type' => DotEnv::SELECT,
                'options' => ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'array'],
            ],
            'QUEUE_DRIVER' => [
                'prompt' => 'What queue driver do you want to use?',
                'type' => DotEnv::SELECT,
                'options' => ['null', 'sync', 'database', 'beanstalkd', 'sqs', 'iron', 'redis'],
            ],

            'MAIL_DRIVER' => [
                'prompt' => 'What mail driver do you want to use?',
                'type' => DotEnv::SELECT,
                'options' => ['smtp', 'mail', 'sendmail', 'mailgun', 'mandrill', 'ses', 'log'],
            ],
            'MAIL_HOST' => [
                'prompt' => 'Set mail server hostname.',
                'type' => DotEnv::INPUT,
            ],
            'MAIL_PORT' => [
                'prompt' => 'Set it\'s port.',
                'type' => DotEnv::INPUT,
            ],
            'MAIL_USERNAME' => [
                'prompt' => 'Provide username to connect to mail server.',
                'type' => DotEnv::INPUT,
            ],
            'MAIL_PASSWORD' => [
                'prompt' => 'Provide password to connect to mail server.',
                'type' => DotEnv::INPUT,
            ],
            'MAIL_ENCRYPTION' => [
                'prompt' => 'Set mail encryption, if you want.',
                'type' => DotEnv::INPUT,
            ],
            'PUSHER_KEY' => [
                'prompt' => 'Set Pusher Key.',
                'type' => DotEnv::INPUT,
            ],
            'PUSHER_SECRET' => [
                'prompt' => 'Set Pusher secret.',
                'type' => DotEnv::INPUT,
            ],
            'PUSHER_APP_ID' => [
                'prompt' => 'Set pusher App ID.',
                'type' => DotEnv::INPUT,
            ],
        ]
    ]

];