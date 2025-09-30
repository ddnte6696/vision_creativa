<?php
// config.php - Configuración base del proyecto
session_start();

// Configuración de la base URL
define('BASE_URL', 'https://omnibus-guadalajara.com/vision_creativa/');

// Configuración de la base de datos (heredada de db_config.php)
require_once __DIR__ . '/db_config.php';

// Configuración del entorno
define('ENVIRONMENT', 'development'); // 'development' o 'production'
define('DEBUG_MODE', true);

// Configuración de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para logging
function logError($message, $context = []) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}";
    if (!empty($context)) {
        $logMessage .= " Context: " . json_encode($context);
    }
    error_log($logMessage . PHP_EOL, 3, __DIR__ . '/logs/error.log');
}

// Crear directorio de logs si no existe
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
?>

