<?php
// Configuración MercadoPago para Vision Creativa
class MercadoPagoConfig {
    private static $instance = null;
    
    // Credenciales MercadoPago (usar variables de entorno en producción)
    const MP_ACCESS_TOKEN = 'TEST-ACCESS-TOKEN'; // Cambiar por $_ENV['MP_ACCESS_TOKEN'] en producción
    const MP_PUBLIC_KEY = 'TEST-PUBLIC-KEY'; // Cambiar por $_ENV['MP_PUBLIC_KEY'] en producción
    const MP_CLIENT_ID = 'TEST-CLIENT-ID'; // Cambiar por $_ENV['MP_CLIENT_ID'] en producción
    const MP_CLIENT_SECRET = 'TEST-CLIENT-SECRET'; // Cambiar por $_ENV['MP_CLIENT_SECRET'] en producción
    
    // URLs de retorno
    const SUCCESS_URL = 'https://vision-creativa.com/payments/views/success.php';
    const FAILURE_URL = 'https://vision-creativa.com/payments/views/failure.php';
    const PENDING_URL = 'https://vision-creativa.com/payments/views/pending.php';
    const WEBHOOK_URL = 'https://vision-creativa.com/payments/webhooks/mercadopago_webhook.php';
    
    // Configuración del sistema
    const CURRENCY = 'MXN';
    const COUNTRY = 'MX';
    const SITE_ID = 'MLM';
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getAccessToken() {
        return self::MP_ACCESS_TOKEN;
    }
    
    public function getPublicKey() {
        return self::MP_PUBLIC_KEY;
    }
    
    public function getReturnUrls() {
        return [
            'success' => self::SUCCESS_URL,
            'failure' => self::FAILURE_URL,
            'pending' => self::PENDING_URL
        ];
    }
    
    public function getWebhookUrl() {
        return self::WEBHOOK_URL;
    }
}
?>
