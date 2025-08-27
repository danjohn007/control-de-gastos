<?php
/**
 * Configuración principal del sistema
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'control_gastos');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Control de Gastos');
define('APP_VERSION', '1.0.0');

// URL base - se configura automáticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script);
if ($base_path === '/') {
    $base_path = '';
}
define('BASE_URL', $protocol . '://' . $host . $base_path);

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de archivos
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}