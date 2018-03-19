<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_ASSOC,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection Conf Mode
    |--------------------------------------------------------------------------
    |
    | when your database configuration read from zookeeper, you should switch
    | this on, then you may give a zk_path to the specified database connection
    | below which you want.
    |
   */

    'read_connections_from_zk' => env('DB_USE_ZK_CONF', true),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mysql-base' => [
            'driver'    => 'mysql',
            'is_zk'     => env('DB_BASE_USE_ZK', false),
            'zk_path'   => env('DB_BASE_ZK_PATH'),
            'host'      => env('DB_BASE_HOST', 'localhost'),
            'database'  => env('DB_BASE_DATABASE', 'forge'),
            'username'  => env('DB_BASE_USERNAME', 'forge'),
            'password'  => env('DB_BASE_PASSWORD', ''),
            'charset'   => env('DB_BASE_CHARSET', 'utf8'),
            'collation' => env('DB_BASE_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => env('DB_BASE_PREFIX', ''),
            'strict'    => false,
        ],
        'mysql-scm' => [
            'driver'    => 'mysql',
            'is_zk'     => env('DB_SCM_USE_ZK', false),
            'zk_path'   => env('DB_SCM_ZK_PATH'),
            'host'      => env('DB_SCM_HOST', 'localhost'),
            'database'  => env('DB_SCM_DATABASE', 'forge'),
            'username'  => env('DB_SCM_USERNAME', 'forge'),
            'password'  => env('DB_SCM_PASSWORD', ''),
            'charset'   => env('DB_SCM_CHARSET', 'utf8'),
            'collation' => env('DB_SCM_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => env('DB_SCM_PREFIX', ''),
            'strict'    => false,
        ],
        'mysql-sn' => [
            'driver'    => 'mysql',
            'is_zk'     => env('DB_SN_USE_ZK', false),
            'zk_path'   => env('DB_SN_ZK_PATH'),
            'host'      => env('DB_SN_HOST', 'localhost'),
            'database'  => env('DB_SN_DATABASE', 'forge'),
            'username'  => env('DB_SN_USERNAME', 'forge'),
            'password'  => env('DB_SN_PASSWORD', ''),
            'charset'   => env('DB_SN_CHARSET', 'utf8'),
            'collation' => env('DB_SN_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => env('DB_SN_PREFIX', ''),
            'strict'    => false,
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => env('DB_CHARSET', 'utf8'),
            'prefix'   => env('DB_PREFIX', ''),
            'schema'   => env('DB_SCHEMA', 'public'),
        ],

        'zk-example' => [
            'zk_path' => env('ZK_PATH_EXAMPLE'),
            'driver'    => 'mysql',
            'host'     => env('EXAMPLE_DB_HOST', 'localhost'),
            'database' => env('EXAMPLE_DB_DATABASE', 'forge'),
            'username' => env('EXAMPLE_DB_USERNAME', 'forge'),
            'password' => env('EXAMPLE_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis_zk' => [
        'use_zk' => env('REDIS_USE_ZK_CONF', true),

        'path' => [
            'default' => env('ZK_PATH_REDIS'),
        ],
    ],

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
    ],

];