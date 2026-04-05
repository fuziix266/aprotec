<?php

use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;

return [
    // Configuración de rutas de la aplicación (VehiculosQr)
    'app_config' => [
        'base_url' => '/',
        'base_path' => __DIR__ . '/../../public',
        'temp_path' => __DIR__ . '/../../public/assets/temp',
        'upload_path' => __DIR__ . '/../../public/assets/uploads',
    ],

    // Configuración de base de datos
    'db' => [
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=DBNAMEAQUI;host=localhost;charset=utf8',
        'username' => 'root',
        'password' => '',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
        ],
    ],

    // Service Manager
    'service_manager' => [
        'factories' => [
            \Laminas\Db\Adapter\AdapterInterface::class => \Laminas\Db\Adapter\AdapterServiceFactory::class,
        ],
        'aliases' => [
            'db' => \Laminas\Db\Adapter\AdapterInterface::class,
            \Laminas\Db\Adapter\Adapter::class => \Laminas\Db\Adapter\AdapterInterface::class,
        ],
    ],

    // Configuración de sesiones - usando filesystem nativo de PHP (no SessionArrayStorage)
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 8, // 8 horas
        'gc_maxlifetime'  => 60 * 60 * 24 * 30, // 30 días
        'name'            => 'aprotec_session',
        'cookie_httponly'  => true,
        'cookie_secure'   => false, // false para compatibilidad con proxies inversos
        'use_cookies'     => true,
        'cookie_path'     => '/',
        'use_strict_mode' => false,
        'save_path'       => '/var/www/html/data/sessions',
    ],
    'session_manager' => [
        // Validators deshabilitados: causan problemas en Docker con proxy inverso (Traefik)
        // ya que RemoteAddr cambia entre requests detrás del proxy
        'validators' => [],
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionStorage::class,
    ],
];
