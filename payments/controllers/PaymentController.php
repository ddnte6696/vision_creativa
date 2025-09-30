<?php
require_once '../config/mercadopago_config.php';
require_once '../services/PaymentService.php';
require_once '../utils/mp_functions.php';

class PaymentController {
    private $paymentService;
    private $config;
    
    public function __construct() {
        $this->config = MercadoPagoConfig::getInstance();
        $this->paymentService = new PaymentService();
    }
    
    public function createPreference($orderData) {
        try {
            // Validar datos de entrada
            if (!$this->validateOrderData($orderData)) {
                throw new Exception('Datos de orden inválidos');
            }
            
            // Crear preferencia en MercadoPago
            $preference = $this->paymentService->createPreference($orderData);
            
            // Guardar transacción pendiente
            $this->paymentService->savePendingTransaction($orderData, $preference['id']);
            
            return [
                'success' => true,
                'preference_id' => $preference['id'],
                'init_point' => $preference['init_point']
            ];
            
        } catch (Exception $e) {
            error_log('Error creating preference: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function processPayment($paymentData) {
        try {
            return $this->paymentService->processPayment($paymentData);
        } catch (Exception $e) {
            error_log('Error processing payment: ' . $e->getMessage());
            return false;
        }
    }
    
    public function handleWebhook($webhookData) {
        try {
            return $this->paymentService->handleWebhookNotification($webhookData);
        } catch (Exception $e) {
            error_log('Error handling webhook: ' . $e->getMessage());
            return false;
        }
    }
    
    private function validateOrderData($data) {
        $required = ['items', 'payer', 'total_amount'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
    
    public function getPaymentStatus($paymentId) {
        return $this->paymentService->getPaymentStatus($paymentId);
    }
}
?>
