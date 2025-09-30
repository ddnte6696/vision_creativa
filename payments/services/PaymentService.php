<?php
// lib/payments/PaymentService.php - Actualizado para la estructura existente
require_once __DIR__ . '/../../db_config.php';
require_once __DIR__ . '/config.php';

class PaymentService {
    private $db;  
    private $accessToken;
    private $publicKey;
    
    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->db->connect_error) {
            throw new Exception('Database connection failed: ' . $this->db->connect_error);
        }
        
        $this->db->set_charset('utf8mb4');
        $this->loadMercadoPagoConfig();
    }
    
    private function loadMercadoPagoConfig() {
        $stmt = $this->db->prepare("SELECT access_token, public_key FROM mp_config WHERE is_active = 1 AND environment = ? LIMIT 1");
        $environment = defined('MP_ENVIRONMENT') ? MP_ENVIRONMENT : 'sandbox';
        $stmt->bind_param("s", $environment);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($config = $result->fetch_assoc()) {
            $this->accessToken = $config['access_token'];
            $this->publicKey = $config['public_key'];
        } else {
            $this->accessToken = getMercadoPagoToken();
            $this->publicKey = getMercadoPagoPublicKey();
        }
        
        $stmt->close();
    }
    
    /**
     * Crear preferencia de pago para Vision Creativa
     */
    public function createPreference($cartItems, $userInfo, $shippingAddress, $orderType = 'b2c') {
        try {
            // Validar datos de entrada
            if (empty($cartItems) || empty($userInfo) || empty($shippingAddress)) {
                throw new Exception('Datos incompletos para crear la preferencia');
            }
            
            // Formatear items para Mercado Pago
            $items = [];
            $totalAmount = 0;
            
            foreach ($cartItems as $item) {
                $unitPrice = floatval($item['price']);
                $quantity = intval($item['quantity']);
                $subtotal = $unitPrice * $quantity;
                
                $items[] = [
                    'id' => strval($item['variacionId']),
                    'title' => $item['productName'],
                    'description' => $item['description'] ?? 'Producto Vision Creativa',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'currency_id' => 'MXN'
                ];
                
                $totalAmount += $subtotal;
            }
            
            // Generar referencia externa única
            $externalReference = $this->generateExternalReference();
            
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
                'notification_url' => MP_WEBHOOK_URL,
                'back_urls' => [
                    'success' => MP_SUCCESS_URL . "?external_reference={$externalReference}",
                    'failure' => MP_FAILURE_URL . "?external_reference={$externalReference}",
                    'pending' => MP_PENDING_URL . "?external_reference={$externalReference}"
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
                    'user_id' => $userInfo['id'],
                    'order_type' => $orderType,
                    'shipping_address_id' => $shippingAddress['id'] ?? null,
                    'total_items' => count($cartItems)
                ]
            ];
            
            // Realizar petición a Mercado Pago
            $response = $this->makeApiRequest('POST', 'checkout/preferences', $preferenceData);
            
            if ($response && isset($response['init_point'])) {
                // Guardar carrito abandonado para tracking/recovery
                $this->saveAbandonedCart($userInfo['id'], $response['id'], $externalReference, $cartItems, $shippingAddress, $totalAmount);
                
                // Log de la transacción
                $this->logTransaction($externalReference, 'preference_created', [
                    'preference_id' => $response['id'],
                    'total_amount' => $totalAmount,
                    'items_count' => count($cartItems)
                ]);
                
                return $response;
            }
            
            throw new Exception('No se pudo crear la preferencia de pago');
            
        } catch (Exception $e) {
            error_log("Error creating MP preference: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener detalles de un pago desde Mercado Pago
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
     * Procesar notificación de webhook
     */
    public function processWebhookNotification($notificationData) {
        try {
            // Guardar notificación raw
            $this->saveNotification($notificationData);
            
            if (isset($notificationData['data']['id'])) {
                $paymentId = $notificationData['data']['id'];
                $paymentDetails = $this->getPaymentDetails($paymentId);
                
                if ($paymentDetails) {
                    // Procesar según el estado
                    switch ($paymentDetails['status']) {
                        case 'approved':
                            return $this->processApprovedPayment($paymentDetails);
                        case 'pending':
                            return $this->processPendingPayment($paymentDetails);
                        case 'rejected':
                        case 'cancelled':
                            return $this->processRejectedPayment($paymentDetails);
                        default:
                            $this->logTransaction($paymentDetails['external_reference'] ?? '', 'unknown_status', $paymentDetails);
                            return true;
                    }
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error processing webhook: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar pago aprobado
     */
    private function processApprovedPayment($paymentDetails) {
        try {
            $externalRef = $paymentDetails['external_reference'];
            $paymentId = $paymentDetails['id'];
            $amount = $paymentDetails['transaction_amount'];
            
            // Verificar si el pedido ya existe
            $stmt = $this->db->prepare("SELECT id FROM pedidos WHERE external_reference = ?");
            $stmt->bind_param("s", $externalRef);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Pedido ya existe, solo actualizar
                $pedido = $result->fetch_assoc();
                $this->updateOrderPaymentStatus($pedido['id'], 'paid', $paymentId);
            } else {
                // Crear nuevo pedido
                $this->createOrderFromPayment($paymentDetails);
            }
            
            // Marcar carrito como recuperado
            $this->markCartAsRecovered($externalRef);
            
            $this->logTransaction($externalRef, 'payment_approved', [
                'payment_id' => $paymentId,
                'amount' => $amount
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error processing approved payment: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear pedido desde pago aprobado - Compatible con estructura existente
     */
    private function createOrderFromPayment($paymentDetails) {
        try {
            $externalRef = $paymentDetails['external_reference'];
            
            // Obtener datos del carrito abandonado
            $stmt = $this->db->prepare("SELECT * FROM carritos_abandonados WHERE external_reference = ? AND recovered = 0");
            $stmt->bind_param("s", $externalRef);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartData = $result->fetch_assoc();
            $stmt->close();
            
            if (!$cartData) {
                throw new Exception("No se encontraron datos del carrito para: " . $externalRef);
            }
            
            $cartItems = json_decode($cartData['cart_data'], true);
            $shippingData = json_decode($cartData['shipping_data'], true);
            
            // Crear pedido usando la estructura existente pero con campos nuevos
            $stmt = $this->db->prepare("
                INSERT INTO pedidos (
                    id_usuario, tipo_pedido, external_reference, preference_id, payment_id,
                    monto_total, estado_pago, estado_envio, payment_method, 
                    shipping_address_id, fecha_pedido
                ) VALUES (?, 'b2c', ?, ?, ?, ?, 'paid', 'preparando', ?, ?, NOW())
            ");
            
            $paymentMethod = $paymentDetails['payment_type_id'] ?? 'credit_card';
            $shippingAddressId = $this->getOrCreateShippingAddress($cartData['usuario_id'], $shippingData);
            
            $stmt->bind_param("isssdsii", 
                $cartData['usuario_id'],
                $externalRef,
                $cartData['preference_id'],
                $paymentDetails['id'],
                $paymentDetails['transaction_amount'],
                $paymentMethod,
                $shippingAddressId
            );
            
            $stmt->execute();
            $pedidoId = $this->db->insert_id;
            $stmt->close();
            
            // Crear items del pedido
            foreach ($cartItems as $item) {
                $this->createOrderItem($pedidoId, $item);
            }
            
            return $pedidoId;
            
        } catch (Exception $e) {
            error_log("Error creating order from payment: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener o crear dirección de envío
     */
    private function getOrCreateShippingAddress($usuarioId, $shippingData) {
        if (isset($shippingData['id']) && !empty($shippingData['id'])) {
            return $shippingData['id'];
        }
        
        // Crear nueva dirección
        $stmt = $this->db->prepare("
            INSERT INTO direcciones (usuario_id, calle, colonia, ciudad, estado, cp, referencias)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("issssss",
            $usuarioId,
            $shippingData['calle'],
            $shippingData['colonia'],
            $shippingData['ciudad'],
            $shippingData['estado'],
            $shippingData['cp'],
            $shippingData['referencias'] ?? ''
        );
        
        $stmt->execute();
        $addressId = $this->db->insert_id;
        $stmt->close();
        
        return $addressId;
    }
    
    /**
     * Crear item de pedido
     */
    private function createOrderItem($pedidoId, $item) {
        $stmt = $this->db->prepare("
            INSERT INTO pedido_items (
                pedido_id, variacion_id, producto_nombre, cantidad, 
                precio_unitario, subtotal, opciones_personalizacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $subtotal = $item['price'] * $item['quantity'];
        $opciones = json_encode($item['selectedOptions'] ?? []);
        
        $stmt->bind_param("iisidds",
            $pedidoId,
            $item['variacionId'],
            $item['productName'],
            $item['quantity'],
            $item['price'],
            $subtotal,
            $opciones
        );
        
        $stmt->execute();
        $stmt->close();
    }
    
    // Métodos auxiliares...
    private function generateExternalReference() {
        return 'VC-' . date('YmdHis') . '-' . uniqid();
    }
    
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
    
    private function saveAbandonedCart($userId, $preferenceId, $externalRef, $cartItems, $shippingData, $totalAmount) {
        $stmt = $this->db->prepare("
            INSERT INTO carritos_abandonados (
                usuario_id, preference_id, external_reference, cart_data, 
                shipping_data, total_amount, expires_at
            ) VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
        ");
        
        $cartJson = json_encode($cartItems);
        $shippingJson = json_encode($shippingData);
        
        $stmt->bind_param("issssd", $userId, $preferenceId, $externalRef, $cartJson, $shippingJson, $totalAmount);
        $stmt->execute();
        $stmt->close();
    }
    
    private function logTransaction($externalRef, $action, $details) {
        $stmt = $this->db->prepare("
            INSERT INTO transaction_log (external_reference, action, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $detailsJson = json_encode($details);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt->bind_param("sssss", $externalRef, $action, $detailsJson, $ipAddress, $userAgent);
        $stmt->execute();
        $stmt->close();
    }
    
    private function saveNotification($data) {
        // Implementar guardado de notificación raw
        $stmt = $this->db->prepare("
            INSERT INTO mp_notifications (
                payment_id, topic, status, external_reference, 
                preference_id, amount, raw_data
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $paymentId = $data['data']['id'] ?? '';
        $topic = $data['type'] ?? '';
        $status = 'received';
        $externalRef = '';
        $preferenceId = '';
        $amount = 0;
        $rawData = json_encode($data);
        
        $stmt->bind_param("sssssds", $paymentId, $topic, $status, $externalRef, $preferenceId, $amount, $rawData);
        $stmt->execute();
        $stmt->close();
    }
    
    private function markCartAsRecovered($externalRef) {
        $stmt = $this->db->prepare("UPDATE carritos_abandonados SET recovered = 1 WHERE external_reference = ?");
        $stmt->bind_param("s", $externalRef);
        $stmt->execute();
        $stmt->close();
    }
    
    private function updateOrderPaymentStatus($orderId, $status, $paymentId) {
        $stmt = $this->db->prepare("UPDATE pedidos SET payment_status = ?, payment_id = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $paymentId, $orderId);
        $stmt->execute();
        $stmt->close();
    }
    
    private function processPendingPayment($paymentDetails) {
        // Implementar lógica para pagos pendientes
        return true;
    }
    
    private function processRejectedPayment($paymentDetails) {
        // Implementar lógica para pagos rechazados
        return true;
    }
}
?>
