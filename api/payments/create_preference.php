<?php
// api/payments/create_preference.php - API para crear preferencia de pago
session_start();
header('Content-Type: application/json');
require_once '../../payments/services/PaymentService.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['cartItems']) || empty($data['shippingAddress'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }
    
    // Obtener datos del usuario de la sesión
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare("SELECT id, nombre, email, telefono FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    if (!$userInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Crear preferencia de pago
    $paymentService = new PaymentService();
    $preference = $paymentService->createPreference(
        $data['cartItems'],
        $userInfo,
        $data['shippingAddress']
    );
    
    if ($preference && isset($preference['init_point'])) {
        // Guardar en sesión para tracking
        $_SESSION['payment_reference'] = $preference['external_reference'];
        $_SESSION['cart_backup'] = $data['cartItems'];
        
        echo json_encode([
            'success' => true,
            'checkout_url' => $preference['init_point'],
            'preference_id' => $preference['id'],
            'external_reference' => $preference['external_reference']
        ]);
    } else {
        throw new Exception('Error al crear preferencia de pago');
    }
    
} catch (Exception $e) {
    error_log("Payment preference error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
?>
