<?php
require_once '../controllers/PaymentController.php';

header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Obtener datos del webhook
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validar formato de datos
if (!$data || !isset($data['type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request format']);
    exit;
}

try {
    // Procesar webhook
    $paymentController = new PaymentController();
    $result = $paymentController->handleWebhook($data);
    
    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Processing failed']);
    }
    
} catch (Exception $e) {
    error_log('Webhook processing error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>
