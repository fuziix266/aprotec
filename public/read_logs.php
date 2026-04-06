<?php
// Script temporal para leer logs de producción en caso de WSOD.
// Borrar después de usar.

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');

$logs = [
    __DIR__ . '/../data/logs/fatal_errors.log',
    __DIR__ . '/../data/logs/php_errors.log',
    '/var/log/apache2/error.log'
];

echo "=== DIAGNÓSTICO DE WSOD EN PRODUCCIÓN ===\n\n";

foreach ($logs as $logPath) {
    echo "Leyendo archivo: $logPath\n";
    echo str_repeat("-", 50) . "\n";
    if (file_exists($logPath)) {
        if (is_readable($logPath)) {
            $content = file_get_contents($logPath);
            // Mostrar últimos 5000 caracteres para no romper
            if (strlen($content) > 5000) {
                echo "... " . substr($content, -5000);
            } else {
                echo $content ?: "(Archivo vacío)";
            }
        } else {
            echo "Error: El archivo existe pero no tiene permisos de lectura.";
        }
    } else {
        echo "El archivo no existe.";
    }
    echo "\n\n";
}

// Información adicional de PHP y sesión
echo "=== INFO EXTRA ===\n";
echo "session.save_path: " . session_save_path() . "\n";
echo "Usuario PHP: " . get_current_user() . " / exec('whoami'): " . exec('whoami') . "\n";
echo "DB_HOST Env: " . getenv('DB_HOST') . "\n";
