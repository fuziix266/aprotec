<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log persistente para producción (captura errores que display_errors no muestra)
$logDir = __DIR__ . '/../data/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0777, true);
}
ini_set('log_errors', 1);
ini_set('error_log', $logDir . '/php_errors.log');

function debug_fatal_handler() {
    $error = error_get_last();
    if($error !== NULL) {
        $logDir = __DIR__ . '/../data/logs';
        $msg = sprintf(
            "[%s] FATAL: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        @file_put_contents($logDir . '/fatal_errors.log', $msg, FILE_APPEND);

        http_response_code(500);
        echo "<pre>FATAL ERROR:\n";
        print_r($error);
        echo "</pre>";
    }
}
register_shutdown_function("debug_fatal_handler");

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Laminas\Mvc\Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = Laminas\Stdlib\ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Laminas\Mvc\Application::init($appConfig)->run();
