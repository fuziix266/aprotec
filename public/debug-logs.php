<?php
// Script temporal de diagnóstico - ELIMINAR después de usar
header('Content-Type: text/plain');

$logDir = __DIR__ . '/../data/logs';

echo "=== PHP Error Log ===\n";
$phpLog = $logDir . '/php_errors.log';
if (file_exists($phpLog)) {
    $lines = file($phpLog);
    echo "Total lines: " . count($lines) . "\n";
    echo implode('', array_slice($lines, -30));
} else {
    echo "File not found: $phpLog\n";
}

echo "\n\n=== Fatal Error Log ===\n";
$fatalLog = $logDir . '/fatal_errors.log';
if (file_exists($fatalLog)) {
    echo file_get_contents($fatalLog);
} else {
    echo "File not found: $fatalLog\n";
}

echo "\n\n=== Apache Error Log (last 20 lines) ===\n";
$apacheLog = '/var/log/apache2/error.log';
if (file_exists($apacheLog)) {
    $lines = file($apacheLog);
    echo implode('', array_slice($lines, -20));
} else {
    echo "File not found: $apacheLog\n";
}

echo "\n\n=== Session Config ===\n";
echo "save_path: " . ini_get('session.save_path') . "\n";
echo "save_path writable: " . (is_writable(ini_get('session.save_path') ?: sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "data/sessions exists: " . (is_dir($logDir . '/../sessions') ? 'YES' : 'NO') . "\n";
echo "data/sessions writable: " . (is_writable($logDir . '/../sessions') ? 'YES' : 'NO') . "\n";

echo "\n\n=== Log Dir ===\n";
echo "Exists: " . (is_dir($logDir) ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($logDir) ? 'YES' : 'NO') . "\n";
if (is_dir($logDir)) {
    echo "Contents: " . implode(', ', scandir($logDir)) . "\n";
}
