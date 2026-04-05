<?php
// Script de diagnóstico directo para el WSOD de /vehiculos/admin
// ELIMINAR después de usar
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO WSOD /vehiculos/admin ===\n\n";

// 1. Verificar archivos del módulo
echo "1. ARCHIVOS DEL MÓDULO:\n";
$files = [
    '/var/www/html/module/Vehiculos/src/Module.php',
    '/var/www/html/module/Vehiculos/src/Controller/AdminController.php',
    '/var/www/html/module/Vehiculos/src/Service/AuthService.php',
    '/var/www/html/module/Vehiculos/src/Repository/QrCodigosRepository.php',
    '/var/www/html/module/Vehiculos/src/Repository/QrUsuariosRepository.php',
    '/var/www/html/module/Vehiculos/config/module.config.php',
    '/var/www/html/config/autoload/local.php',
];
foreach ($files as $f) {
    echo "  " . basename($f) . ": " . (file_exists($f) ? "OK (" . filesize($f) . "b)" : "MISSING!") . "\n";
}

// 2. Verificar sesiones
echo "\n2. SESIONES:\n";
$sessDir = '/var/www/html/data/sessions';
echo "  save_path config: " . ini_get('session.save_path') . "\n";
echo "  data/sessions exists: " . (is_dir($sessDir) ? 'YES' : 'NO') . "\n";
echo "  data/sessions writable: " . (is_dir($sessDir) && is_writable($sessDir) ? 'YES' : 'NO') . "\n";
echo "  sys_get_temp_dir: " . sys_get_temp_dir() . "\n";
echo "  tmp writable: " . (is_writable(sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";

// 3. Probar session_start
echo "\n3. SESSION_START:\n";
try {
    session_start();
    echo "  session_start(): OK (ID: " . session_id() . ")\n";
    session_destroy();
} catch (\Throwable $e) {
    echo "  session_start(): FAILED - " . $e->getMessage() . "\n";
}

// 4. Probar conexión a BD
echo "\n4. BASE DE DATOS:\n";
try {
    $config = include '/var/www/html/config/autoload/local.php';
    $dbConfig = $config['db'] ?? [];
    echo "  driver: " . ($dbConfig['driver'] ?? 'NOT SET') . "\n";
    echo "  dsn: " . ($dbConfig['dsn'] ?? 'NOT SET') . "\n";
    echo "  username: " . ($dbConfig['username'] ?? 'NOT SET') . "\n";
    
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'] ?? '');
    echo "  Connection: OK\n";
    
    // Probar tabla
    $stmt = $pdo->query("SELECT COUNT(*) FROM qr_codigos");
    echo "  qr_codigos count: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM qr_usuarios");
    echo "  qr_usuarios count: " . $stmt->fetchColumn() . "\n";
} catch (\Throwable $e) {
    echo "  DB ERROR: " . $e->getMessage() . "\n";
}

// 5. Probar autoload y ServiceManager
echo "\n5. BOOTSTRAP LAMINAS:\n";
try {
    chdir('/var/www/html/public');
    require '/var/www/html/vendor/autoload.php';
    
    // Verificar que las clases existen
    $classes = [
        'Vehiculos\\Module',
        'Vehiculos\\Controller\\AdminController',
        'Vehiculos\\Service\\AuthService',
        'Vehiculos\\Repository\\QrCodigosRepository',
        'Vehiculos\\Repository\\QrUsuariosRepository',
    ];
    foreach ($classes as $c) {
        echo "  class $c: " . (class_exists($c) ? 'OK' : 'MISSING') . "\n";
    }
    
    // Intentar crear la aplicación
    $appConfig = require '/var/www/html/config/application.config.php';
    echo "  modules: " . implode(', ', $appConfig['modules']) . "\n";
    
    $app = \Laminas\Mvc\Application::init($appConfig);
    echo "  Application::init(): OK\n";
    
    $sm = $app->getServiceManager();
    echo "  ServiceManager: OK\n";
    
    // Probar obtener AuthService
    $authService = $sm->get(\Vehiculos\Service\AuthService::class);
    echo "  AuthService: OK\n";
    
    // Probar obtener AdminController
    $controllerManager = $sm->get('ControllerManager');
    $controller = $controllerManager->get(\Vehiculos\Controller\AdminController::class);
    echo "  AdminController: OK\n";
    
} catch (\Throwable $e) {
    echo "  BOOTSTRAP ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "  Trace:\n" . $e->getTraceAsString() . "\n";
}

// 6. Verificar development.config.php
echo "\n6. DEVELOPMENT CONFIG:\n";
$devConfig = '/var/www/html/config/development.config.php';
echo "  development.config.php: " . (file_exists($devConfig) ? "EXISTS (PROBLEM!)" : "Not found (OK)") . "\n";

echo "\n=== FIN DIAGNÓSTICO ===\n";
