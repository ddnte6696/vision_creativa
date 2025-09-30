<?php
// lib/payments/PaymentService.php - Servicio principal de pagos
require_once __DIR__ . '/config.php';

class PaymentService {
    private $accessToken;
    private $publicKey;
    
    public function __construct() {
        validateMercadoPagoConfig();
        $this->accessToken = getMercadoPagoToken();
        $this->publicKey = getMercadoPagoPublicKey();
    }
    
    /**
     * Crear preferencia de pago para el carrito
     */
    public function createCartPreference($cartItems, $userInfo, $shippingAddress) {
        try {
            $items = [];
            $totalAmount = 0;
            
            // Formatear items del carrito para Mercado Pago
            foreach ($cartItems as $item) {
                $unitPrice = (float)$item['price'];
                $quantity = (int)$item['quantity'];
                
                $items[] = [
                    'id' => (string)$item['variacionId'],
                    'title' => $item['productName'],
                    'description' => $item['description'] ?? 'Producto Vision Creativa',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'currency_id' => 'MXN'
                ];
                
                $totalAmount += $unitPrice * $quantity;
            }
            
            // Generar referencia externa única
            $externalReference = 'VC-' . date('YmdHis') . '-' . uniqid();
            
            $preferenceData = [
                'items' => $items,
                'payer' => [
                    'name' => $userInfo['nombre'],
                    'email' => $userInfo['email'],
                    'phone' => [
                        'area_code' => '',
                        'number' => ''
                    ]
                ],
                'external_reference' => $externalReference,
                'notification_url' => MP_WEBHOOK_URL,
                'back_urls' => [
                    'success' => MP_SUCCESS_URL,
                    'failure' => MP_FAILURE_URL,
                    'pending' => MP_PENDING_URL
                ],
                'auto_return' => 'approved',
                'payment_methods' => [
                    'excluded_payment_types' => [
                        // Excluir pagos en efectivo si no los quieres
                        // ['id' => 'ticket']
                    ],
                    'installments' => 12,
                    'default_installments' => 1
                ],
                'statement_descriptor' => 'Vision Creativa',
                'metadata' => [
                    'user_id' => $userInfo['id'] ?? null,
                    'shipping_address' => json_encode($shippingAddress),
                    'cart_items' => json_encode($cartItems)
                ]
            ];
            
            $result = $this->makeRequest('POST', 'checkout/preferences', $preferenceData);
            
            if ($result && isset($result['init_point'])) {
                // Log exitoso
                error_log("MP Preference created successfully: " . $externalReference);
                return $result;
            }
            
            throw new Exception('No se pudo crear la preferencia de pago');
            
        } catch (Exception $e) {
            error_log("Error creating MP preference: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener detalles de un pago
     */
    public function getPaymentDetails($paymentId) {
        try {
            return $this->makeRequest('GET', "v1/payments/{$paymentId}");
        } catch (Exception $e) {
            error_log("Error getting payment details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar notificación de webhook
     */
    public function processWebhookNotification($data) {
        try {
            if (isset($data['data']['id'])) {
                $paymentId = $data['data']['id'];
                $paymentDetails = $this->getPaymentDetails($paymentId);
                
                if ($paymentDetails) {
                    error_log("Payment status update: " . $paymentId . " - " . $paymentDetails['status']);
                    return $paymentDetails;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("Error processing webhook: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Realizar petición a la API de Mercado Pago
     */
    private function makeRequest($method, $endpoint, $data = null) {
        $url = "https://api.mercadopago.com/{$endpoint}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accessToken,
                'X-Idempotency-Key: ' . uniqid()
            ]
        ]);
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        throw new Exception("API Error [{$httpCode}]: " . $response);
    }
    
    /**
     * Validar signature del webhook (implementar para mayor seguridad)
     */
    public function validateWebhookSignature($headers, $body) {
        // TODO: Implementar validación de signature
        return true;
    }
}
?>
