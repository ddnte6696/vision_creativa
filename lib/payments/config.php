<?php
// lib/payments/config.php - Configuración centralizada de pagos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de Mercado Pago
define('MP_ACCESS_TOKEN_PROD', ''); // Token de producción - CONFIGURAR EN PRODUCCIÓN
define('MP_ACCESS_TOKEN_TEST', 'TEST-3907237835175069-042513-54392cff1eae5de032c47aa617dd5531-1773584073');
define('MP_PUBLIC_KEY_TEST', 'TEST-23fa22ee-e058-41e0-96cc-2798229b16bc');

// Definir BASE_URL si no está definida
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://omnibus-guadalajara.com/vision_creativa/');
}

// URLs de retorno siguiendo el patrón BASE_URL del proyecto
define('MP_SUCCESS_URL', BASE_URL . 'public/payment_success.php');
define('MP_FAILURE_URL', BASE_URL . 'public/payment_failure.php');
define('MP_PENDING_URL', BASE_URL . 'public/payment_pending.php');
define('MP_WEBHOOK_URL', BASE_URL . 'api/payments/webhook.php');

// Configuración del entorno
define('MP_ENVIRONMENT', 'test'); // 'test' o 'production'

/**
 * Obtener el token de acceso según el ambiente
 */
function getMercadoPagoToken() {
    return MP_ENVIRONMENT === 'production' ? MP_ACCESS_TOKEN_PROD : MP_ACCESS_TOKEN_TEST;
}

/**
 * Obtener la clave pública según el ambiente
 */
function getMercadoPagoPublicKey() {
    return MP_PUBLIC_KEY_TEST; // Agregar versión de producción cuando sea necesario
}

/**
 * Validar configuración de Mercado Pago
 */
function validateMercadoPagoConfig() {
    $token = getMercadoPagoToken();
    if (empty($token) || $token === 'TEST-YOUR-TOKEN-HERE') {
        throw new Exception('Token de Mercado Pago no configurado correctamente');
    }
    return true;
}
?>

