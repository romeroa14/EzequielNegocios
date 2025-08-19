<?php

/**
 * Configuración de conexión a la base de datos de producción
 * 
 * Uso:
 * - Para Laravel: Config::set('database.connections.production', require 'database/production_connection.php');
 * - Para comandos: php artisan db:production status
 * - Para psql: php artisan db:psql
 */

return [
    'driver' => 'pgsql',
    'host' => 'ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud',
    'port' => '5432',
    'database' => 'main',
    'username' => 'laravel',
    'password' => 'npg_Gr42CaFlQUKs',
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true,
    ],
];
