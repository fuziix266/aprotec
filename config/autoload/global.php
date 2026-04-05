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
        // URL pública del sitio (usada en QR codes y PDFs)
        'site_url' => getenv('SITE_URL') ?: 'https://www.aprotec.cl',
        // Configuración SMTP para envío de correos
        'smtp' => [
            'host'     => getenv('SMTP_HOST') ?: 'smtp.aprotec.cl',
            'port'     => (int) (getenv('SMTP_PORT') ?: 587),
            'username' => getenv('SMTP_USER') ?: 'noreply@aprotec.cl',
            'password' => getenv('SMTP_PASS') ?: '',
            'ssl'      => getenv('SMTP_SSL') ?: 'tls',
            'from_email' => getenv('SMTP_FROM') ?: 'noreply@aprotec.cl',
            'from_name'  => 'Sistema QR Vehículos - APROTEC',
        ],
    ],

    // NOTA: La configuración 'db' se define en local.php (con variables de entorno)
    // No se define aquí para evitar conflictos con valores placeholder.

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
        'save_path'       => getenv('SESSION_PATH') ?: sys_get_temp_dir(),
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
