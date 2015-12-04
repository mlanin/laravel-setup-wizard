<?php

use Lanin\Laravel\SetupWizard\Commands\Steps\DotEnv as DotEnvStep;

return [
    // Setup title. Shown in the beginning of setup.
    'title' => 'Laravel Setup Wizard',

    // Steps to run. Will be run in the order as they defined in this array.
    'steps' => [
        'dot_env'           => Lanin\Laravel\SetupWizard\Commands\Steps\DotEnv::class,
        'create_database'   => Lanin\Laravel\SetupWizard\Commands\Steps\CreateMysqlDatabase::class,
        'migrate'           => Lanin\Laravel\SetupWizard\Commands\Steps\Migrate::class,
        'seed'              => Lanin\Laravel\SetupWizard\Commands\Steps\Seed::class,
        'create_user'       => Lanin\Laravel\SetupWizard\Commands\Steps\CreateUser::class,
        'optimize'          => Lanin\Laravel\SetupWizard\Commands\Steps\Optimize::class,
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
                'type' => DotEnvStep::SELECT,
                'options' => ['local', 'production'],
            ],
            'APP_DEBUG' => [
                'prompt' => 'Enable debug mode?',
                'type' => DotEnvStep::SELECT,
                'options' => ['true', 'false'],
            ],
            'APP_KEY' => [
                'prompt' => 'Application unique key. For initial setup better to leave random.',
                'type' => DotEnvStep::RANDOM,
            ],

            'DB_CONNECTION' => [
                'prompt' => 'What database connection to use?',
                'type' => DotEnvStep::SELECT,
                'options' => ['mysql', 'sqlite', 'pgsql', 'sqlsrv'],
            ],
            'DB_HOST' => [
                'prompt' => 'Set database host.',
                'type' => DotEnvStep::INPUT,
            ],
            'DB_DATABASE' => [
                'prompt' => 'Set database name.',
                'type' => DotEnvStep::INPUT,
            ],
            'DB_USERNAME' => [
                'prompt' => 'Provide username to connect to the database.',
                'type' => DotEnvStep::INPUT,
            ],
            'DB_PASSWORD' => [
                'prompt' => 'Provide password to use to connect to the database.',
                'type' => DotEnvStep::INPUT,
            ],

            'CACHE_DRIVER' => [
                'prompt' => 'What cache driver do you want to use?',
                'type' => DotEnvStep::SELECT,
                'options' => ['apc', 'array', 'database', 'file', 'memcached', 'redis'],
            ],
            'SESSION_DRIVER' => [
                'prompt' => 'What session driver do you want to use?',
                'type' => DotEnvStep::SELECT,
                'options' => ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'array'],
            ],
            'QUEUE_DRIVER' => [
                'prompt' => 'What queue driver do you want to use?',
                'type' => DotEnvStep::SELECT,
                'options' => ['null', 'sync', 'database', 'beanstalkd', 'sqs', 'iron', 'redis'],
            ],

            'MAIL_DRIVER' => [
                'prompt' => 'What mail driver do you want to use?',
                'type' => DotEnvStep::SELECT,
                'options' => ['smtp', 'mail', 'sendmail', 'mailgun', 'mandrill', 'ses', 'log'],
            ],
            'MAIL_HOST' => [
                'prompt' => 'Set mail server hostname.',
                'type' => DotEnvStep::INPUT,
            ],
            'MAIL_PORT' => [
                'prompt' => 'Set it\'s port.',
                'type' => DotEnvStep::INPUT,
            ],
            'MAIL_USERNAME' => [
                'prompt' => 'Provide username to connect to mail server.',
                'type' => DotEnvStep::INPUT,
            ],
            'MAIL_PASSWORD' => [
                'prompt' => 'Provide password to connect to mail server.',
                'type' => DotEnvStep::INPUT,
            ],
            'MAIL_ENCRYPTION' => [
                'prompt' => 'Set mail encryption, if you want.',
                'type' => DotEnvStep::INPUT,
            ],
            'PUSHER_KEY' => [
                'prompt' => 'Set Pusher Key.',
                'type' => DotEnvStep::INPUT,
            ],
            'PUSHER_SECRET' => [
                'prompt' => 'Set Pusher secret.',
                'type' => DotEnvStep::INPUT,
            ],
            'PUSHER_APP_ID' => [
                'prompt' => 'Set pusher App ID.',
                'type' => DotEnvStep::INPUT,
            ],
        ]
    ]

];