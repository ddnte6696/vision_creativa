<?php
// api/payments/webhook.php - Webhook para notificaciones de Mercado Pago
require_once '../../payments/services/PaymentService.php';

// Log de entrada
$input = file_get_contents('php://input');
error_log("MP Webhook received: " . $input);

// Procesar solo notificaciones de payment
if (isset($_GET['type']) && $_GET['type'] === 'payment') {
    try {
        $data = json_decode($input, true);
        
        $paymentService = new PaymentService();
        $result = $paymentService->processWebhook($data);
        
        if ($result) {
            error_log("Webhook processed successfully");
        } else {
            error_log("Webhook processing failed");
        }
        
    } catch (Exception $e) {
        error_log("Webhook error: " . $e->getMessage());
    }
}

// Responder OK a Mercado Pago
http_response_code(200);
echo 'OK';
?>
