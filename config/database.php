<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'logistic'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
            'mysql_3e' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL_3E'),
            // READ  -> ถ้าตั้ง DB_*_3E_READ จะอ่านจาก "โคลน" (เช่น 192.168.1.148) ;
            //          ถ้าไม่ตั้ง จะ fallback ไปใช้ค่าหลัก (พฤติกรรมเดิม 100%)
            // WRITE -> ใช้ค่าหลัก (3e จริง) เสมอ เพราะ printwarehouse ต้อง insert ลงตัวจริงเท่านั้น
            'read' => [
                'host'      => env('DB_HOST_3E_READ', env('DB_HOST_3E', '127.0.0.1')),
                'port'      => env('DB_PORT_3E_READ', env('DB_PORT_3E', '3306')),
                'database'  => env('DB_DATABASE_3E_READ', env('DB_DATABASE_3E', 'forge')),
                'username'  => env('DB_USERNAME_3E_READ', env('DB_USERNAME_3E', 'forge')),
                'password'  => env('DB_PASSWORD_3E_READ', env('DB_PASSWORD_3E', '')),
                'charset'   => env('DB_CHARSET_3E_READ', env('DB_CHARSET_3E', 'utf8')),
                'collation' => env('DB_COLLATION_3E_READ', env('DB_COLLATION_3E', 'utf8_general_ci')),
            ],
            'write' => [
                'host'      => env('DB_HOST_3E', '127.0.0.1'),
                'port'      => env('DB_PORT_3E', '3306'),
                'database'  => env('DB_DATABASE_3E', 'forge'),
                'username'  => env('DB_USERNAME_3E', 'forge'),
                'password'  => env('DB_PASSWORD_3E', ''),
                'charset'   => env('DB_CHARSET_3E', 'utf8'),
                'collation' => env('DB_COLLATION_3E', 'utf8_general_ci'),
            ],
            'sticky' => true,
            'unix_socket' => env('DB_SOCKET_3E', ''),
            'charset' => env('DB_CHARSET_3E', 'utf8'),
            'collation' => env('DB_COLLATION_3E', 'utf8_general_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

            'pgsql' => [
                'driver' => 'pgsql',
                'host' => env('DB_PG_HOST', '192.168.1.148'),
                'port' => env('DB_PG_PORT', '5432'),
                'database' => env('DB_PG_DATABASE', '3E'),
                'username' => env('DB_PG_USERNAME', 'postgres'),
                'password' => env('DB_PG_PASSWORD', 'hikari2025'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
           
        ],
        // อ่านอย่างเดียว (ไม่มี write จากฝั่ง logistic เลย)
        // สลับไปอ่านจาก "โคลน" (MySQL ที่ 192.168.1.148) โดยตั้งใน .env:
        //   DB_DRIVER_MS=mysql  DB_HOST_MS=192.168.1.148  DB_PORT_MS=3306
        //   DB_USERNAME_MS=logistic_ro  DB_PASSWORD_MS=...  DB_CHARSET_MS=utf8mb4  DB_COLLATION_MS=utf8mb4_general_ci
        // ถ้าไม่ตั้ง -> ยังเป็น sqlsrv ชี้ .115 ตามเดิม (dev ไม่เปลี่ยน)
        'mssql_account03' => [
            'driver'    => env('DB_DRIVER_MS', 'sqlsrv'),
            'host'      => env('DB_HOST_MS', '192.168.1.115'),
            'port'      => env('DB_PORT_MS', '1433'),
            'database'  => env('DB_DATABASE_MS', 'account03'),
            'username'  => env('DB_USERNAME_MS', 'powerbi'),
            'password'  => env('DB_PASSWORD_MS', 'powerbi'),
            'charset'   => env('DB_CHARSET_MS', 'utf8'),
            'collation' => env('DB_COLLATION_MS', 'utf8_general_ci'),
            'prefix'    => '',
            'prefix_indexes' => true,
            'strict'    => false,
            'options'   => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
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
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
