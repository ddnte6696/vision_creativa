<?php
// api/payments/webhook.php - Webhook para notificaciones de Mercado Pago
require_once '../../db_config.php';
require_once '../../lib/payments/PaymentService.php';

// Log de la notificación recibida
$input = file_get_contents('php://input');
$headers = getallheaders();
error_log("MP Webhook received: " . $input);

// Verificar que es una notificación válida
if (isset($_GET['type']) && $_GET['type'] === 'payment') {
    try {
        $data = json_decode($input, true);
        
        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];
            
            // Obtener detalles del pago
            $paymentService = new PaymentService();
            $paymentDetails = $paymentService->getPaymentDetails($paymentId);
            
            if ($paymentDetails) {
                $status = $paymentDetails['status'];
                $externalReference = $paymentDetails['external_reference'] ?? '';
                $amount = $paymentDetails['transaction_amount'] ?? 0;
                
                // Log del estado del pago
                error_log("Payment {$paymentId} status: {$status}, reference: {$externalReference}");
                
                // Procesar según el estado del pago
                switch ($status) {
                    case 'approved':
                        // Pago aprobado - procesar pedido
                        $orderResult = processApprovedPayment($paymentDetails, $conn);
                        error_log("Order processing result: " . json_encode($orderResult));
                        break;
                        
                    case 'pending':
                        // Pago pendiente - mantener en espera
                        error_log("Payment {$paymentId} is pending");
                        break;
                        
                    case 'rejected':
                        // Pago rechazado - liberar stock si se había reservado
                        error_log("Payment {$paymentId} was rejected");
                        break;
                        
                    default:
                        error_log("Payment {$paymentId} has status: {$status}");
                        break;
                }
                
                // Guardar notificación en base de datos
                $stmt = $conn->prepare("INSERT INTO payment_notifications (payment_id, status, external_reference, amount, raw_data, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $rawData = json_encode($paymentDetails);
                $stmt->bind_param("sssds", $paymentId, $status, $externalReference, $amount, $rawData);
                $stmt->execute();
                $stmt->close();
            }
        }
    } catch (Exception $e) {
        error_log("Webhook processing error: " . $e->getMessage());
    }
}

// Función para procesar pago aprobado
function processApprovedPayment($paymentDetails, $conn) {
    try {
        $externalReference = $paymentDetails['external_reference'];
        $paymentId = $paymentDetails['id'];
        $amount = $paymentDetails['transaction_amount'];
        
        // Extraer datos del metadata si están disponibles
        $metadata = $paymentDetails['metadata'] ?? [];
        $userId = $metadata['user_id'] ?? null;
        $cartItems = json_decode($metadata['cart_items'] ?? '[]', true);
        $shippingAddress = json_decode($metadata['shipping_address'] ?? '{}', true);
        
        if (empty($cartItems)) {
            throw new Exception("No cart items found in payment metadata");
        }
        
        // Crear el pedido en la base de datos
        $stmt = $conn->prepare("INSERT INTO pedidos (user_id, external_reference, payment_id, total, status, payment_status, created_at) VALUES (?, ?, ?, ?, 'processing', 'paid', NOW())");
        $stmt->bind_param("issd", $userId, $externalReference, $paymentId, $amount);
        $stmt->execute();
        $orderId = $conn->insert_id;
        $stmt->close();
        
        // Insertar items del pedido
        foreach ($cartItems as $item) {
            $stmt = $conn->prepare("INSERT INTO pedido_items (pedido_id, variacion_id, producto_nombre, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisid", $orderId, $item['variacionId'], $item['productName'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }
        
        // TODO: Actualizar stock de productos
        // TODO: Enviar email de confirmación
        
        return ['success' => true, 'order_id' => $orderId];
        
    } catch (Exception $e) {
        error_log("Error processing approved payment: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Responder a Mercado Pago
http_response_code(200);
echo 'OK';
?>
