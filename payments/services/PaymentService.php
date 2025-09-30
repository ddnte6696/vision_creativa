<?php
// payments/services/PaymentService.php - Fusionado con mejoras de Mercado Pago
require_once __DIR__ . '/../../db_config.php';

class PaymentService {
    private $conn;
    private $accessToken;
    private $publicKey;
    
    public function __construct() {
        // Conectar usando db_config.php existente
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            throw new Exception('Database connection failed: ' . $this->conn->connect_error);
        }
        
        $this->conn->set_charset('utf8mb4');
        $this->loadMercadoPagoConfig();
    }
    
    /**
     * Cargar configuración de Mercado Pago desde base de datos
     */
    private function loadMercadoPagoConfig() {
        $stmt = $this->conn->prepare("SELECT access_token, public_key FROM mp_config WHERE is_active = 1 AND environment = 'sandbox' LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($config = $result->fetch_assoc()) {
            $this->accessToken = $config['access_token'];
            $this->publicKey = $config['public_key'];
        } else {
            throw new Exception('Configuración de Mercado Pago no encontrada');
        }
        
        $stmt->close();
    }
    
    /**
     * Crear preferencia de pago para carrito
     */
    public function createPreference($cartItems, $userInfo, $shippingAddress, $orderType = 'b2c') {
        try {
            // Validar datos de entrada
            if (empty($cartItems) || empty($userInfo) || empty($shippingAddress)) {
                throw new Exception('Datos incompletos para crear preferencia');
            }
            
            // Formatear items para Mercado Pago
            $items = [];
            $totalAmount = 0;
            
            foreach ($cartItems as $item) {
                $unitPrice = floatval($item['price']);
                $quantity = intval($item['quantity']);
                
                $items[] = [
                    'id' => strval($item['variacionId']),
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
            
            // Datos de la preferencia
            $preferenceData = [
                'items' => $items,
                'payer' => [
                    'name' => $userInfo['nombre'],
                    'email' => $userInfo['email'],
                    'phone' => [
                        'area_code' => '',
                        'number' => $userInfo['telefono'] ?? ''
                    ]
                ],
                'external_reference' => $externalReference,
                'notification_url' => 'https://omnibus-guadalajara.com/vision_creativa/api/payments/webhook.php',
                'back_urls' => [
                    'success' => 'https://omnibus-guadalajara.com/vision_creativa/public/payment_success.php?ref=' . $externalReference,
                    'failure' => 'https://omnibus-guadalajara.com/vision_creativa/public/payment_failure.php?ref=' . $externalReference,
                    'pending' => 'https://omnibus-guadalajara.com/vision_creativa/public/payment_pending.php?ref=' . $externalReference
                ],
                'auto_return' => 'approved',
                'payment_methods' => [
                    'installments' => 12,
                    'default_installments' => 1
                ],
                'statement_descriptor' => 'Vision Creativa',
                'metadata' => [
                    'user_id' => $userInfo['id'],
                    'order_type' => $orderType,
                    'shipping_address_id' => $shippingAddress['id'] ?? null
                ]
            ];
            
            // Realizar petición a Mercado Pago
            $response = $this->makeApiRequest('POST', 'checkout/preferences', $preferenceData);
            
            if ($response && isset($response['init_point'])) {
                // Guardar carrito para recovery
                $this->saveAbandonedCart($userInfo['id'], $response['id'], $externalReference, $cartItems, $shippingAddress, $totalAmount);
                
                // Log de transacción
                $this->logTransaction($externalReference, 'preference_created', [
                    'preference_id' => $response['id'],
                    'total_amount' => $totalAmount,
                    'user_id' => $userInfo['id']
                ]);
                
                return $response;
            }
            
            throw new Exception('Error al crear preferencia de Mercado Pago');
            
        } catch (Exception $e) {
            error_log("PaymentService Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener detalles de pago desde Mercado Pago
     */
    public function getPaymentDetails($paymentId) {
        try {
            return $this->makeApiRequest('GET', "v1/payments/{$paymentId}");
        } catch (Exception $e) {
            error_log("Error getting payment details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar webhook de Mercado Pago
     */
    public function processWebhook($data) {
        try {
            // Guardar notificación
            $this->saveNotification($data);
            
            if (isset($data['data']['id'])) {
                $paymentId = $data['data']['id'];
                $paymentDetails = $this->getPaymentDetails($paymentId);
                
                if ($paymentDetails) {
                    switch ($paymentDetails['status']) {
                        case 'approved':
                            return $this->processApprovedPayment($paymentDetails);
                        case 'pending':
                            return $this->processPendingPayment($paymentDetails);
                        case 'rejected':
                        case 'cancelled':
                            return $this->processRejectedPayment($paymentDetails);
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Webhook processing error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar pago aprobado
     */
    private function processApprovedPayment($paymentDetails) {
        try {
            $externalRef = $paymentDetails['external_reference'];
            
            // Verificar si el pedido ya existe
            $stmt = $this->conn->prepare("SELECT id FROM pedidos WHERE external_reference = ?");
            $stmt->bind_param("s", $externalRef);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                // Crear nuevo pedido
                $orderId = $this->createOrderFromPayment($paymentDetails);
                
                // Marcar carrito como recuperado
                $stmt = $this->conn->prepare("UPDATE carritos_abandonados SET recovered = 1 WHERE external_reference = ?");
                $stmt->bind_param("s", $externalRef);
                $stmt->execute();
                $stmt->close();
            } else {
                // Actualizar pedido existente
                $order = $result->fetch_assoc();
                $stmt = $this->conn->prepare("UPDATE pedidos SET payment_id = ?, estado_pago = 'paid' WHERE id = ?");
                $stmt->bind_param("si", $paymentDetails['id'], $order['id']);
                $stmt->execute();
                $stmt->close();
            }
            
            $this->logTransaction($externalRef, 'payment_approved', $paymentDetails);
            return true;
            
        } catch (Exception $e) {
            error_log("Error processing approved payment: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear pedido desde pago aprobado
     */
    private function createOrderFromPayment($paymentDetails) {
        // Obtener datos del carrito abandonado
        $stmt = $this->conn->prepare("SELECT * FROM carritos_abandonados WHERE external_reference = ?");
        $stmt->bind_param("s", $paymentDetails['external_reference']);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartData = $result->fetch_assoc();
        $stmt->close();
        
        if (!$cartData) {
            throw new Exception("Datos de carrito no encontrados");
        }
        
        // Crear pedido
        $stmt = $this->conn->prepare("
            INSERT INTO pedidos (
                id_usuario, tipo_pedido, external_reference, preference_id, payment_id,
                monto_total, estado_pago, estado_envio, payment_method, 
                shipping_address_id, fecha_pedido
            ) VALUES (?, 'b2c', ?, ?, ?, ?, 'paid', 'preparando', ?, ?, NOW())
        ");
        
        $paymentMethod = $paymentDetails['payment_type_id'] ?? 'credit_card';
        $shippingData = json_decode($cartData['shipping_data'], true);
        
        $stmt->bind_param("isssdsi",
            $cartData['usuario_id'],
            $paymentDetails['external_reference'],
            $cartData['preference_id'],
            $paymentDetails['transaction_amount'],
            $paymentMethod,
            $shippingData['id'] ?? null
        );
        
        $stmt->execute();
        $orderId = $this->conn->insert_id;
        $stmt->close();
        
        // Crear items del pedido
        $cartItems = json_decode($cartData['cart_data'], true);
        foreach ($cartItems as $item) {
            $stmt = $this->conn->prepare("
                INSERT INTO pedido_items (pedido_id, variacion_id, producto_nombre, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->bind_param("iisaid", $orderId, $item['variacionId'], $item['productName'], $item['quantity'], $item['price'], $subtotal);
            $stmt->execute();
            $stmt->close();
        }
        
        return $orderId;
    }
    
    // Métodos auxiliares
    private function makeApiRequest($method, $endpoint, $data = null) {
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
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        throw new Exception("API Error [{$httpCode}]: " . $response);
    }
    
    private function saveAbandonedCart($userId, $preferenceId, $externalRef, $cartItems, $shippingData, $totalAmount) {
        $stmt = $this->conn->prepare("
            INSERT INTO carritos_abandonados (usuario_id, preference_id, external_reference, cart_data, shipping_data, total_amount, expires_at)
            VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
        ");
        
        $cartJson = json_encode($cartItems);
        $shippingJson = json_encode($shippingData);
        
        $stmt->bind_param("issssd", $userId, $preferenceId, $externalRef, $cartJson, $shippingJson, $totalAmount);
        $stmt->execute();
        $stmt->close();
    }
    
    private function logTransaction($externalRef, $action, $details) {
        $stmt = $this->conn->prepare("
            INSERT INTO transaction_log (external_reference, action, details, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        
        $detailsJson = json_encode($details);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt->bind_param("ssss", $externalRef, $action, $detailsJson, $ipAddress);
        $stmt->execute();
        $stmt->close();
    }
    
    private function saveNotification($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO mp_notifications (payment_id, topic, status, external_reference, raw_data)
            VALUES (?, ?, 'received', '', ?)
        ");
        
        $paymentId = $data['data']['id'] ?? '';
        $topic = $data['type'] ?? '';
        $rawData = json_encode($data);
        
        $stmt->bind_param("sss", $paymentId, $topic, $rawData);
        $stmt->execute();
        $stmt->close();
    }
    
    private function processPendingPayment($paymentDetails) {
        $this->logTransaction($paymentDetails['external_reference'] ?? '', 'payment_pending', $paymentDetails);
        return true;
    }
    
    private function processRejectedPayment($paymentDetails) {
        $this->logTransaction($paymentDetails['external_reference'] ?? '', 'payment_rejected', $paymentDetails);
        return true;
    }
}
?>
