<?php
// api/payments/create_preference.php - API para crear preferencia de pago
session_start();
header('Content-Type: application/json');
require_once '../../db_config.php';
require_once '../../lib/payments/PaymentService.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    if (empty($data['cartItems']) || empty($data['shippingAddress'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
        exit;
    }
    
    // Obtener datos del usuario
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    
    if (!$userInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
        exit;
    }
    
    // Validar items del carrito (verificar stock, precios, etc.)
    foreach ($data['cartItems'] as $item) {
        if (empty($item['variacionId']) || empty($item['productName']) || empty($item['price']) || empty($item['quantity'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Item de carrito inválido.']);
            exit;
        }
        
        // TODO: Verificar que el producto existe y tiene stock
        // $stmt = $conn->prepare("SELECT stock FROM variaciones_producto WHERE id = ?");
        // $stmt->bind_param("i", $item['variacionId']);
        // ... validación de stock
    }
    
    // Crear preferencia de pago
    $paymentService = new PaymentService();
    $preference = $paymentService->createCartPreference(
        $data['cartItems'],
        $userInfo,
        $data['shippingAddress']
    );
    
    if ($preference && isset($preference['init_point'])) {
        // Guardar referencia en sesión para tracking
        $_SESSION['payment_reference'] = $preference['external_reference'];
        $_SESSION['cart_backup'] = $data['cartItems']; // Backup del carrito para recovery
        $_SESSION['shipping_address_backup'] = $data['shippingAddress'];
        
        echo json_encode([
            'success' => true,
            'checkout_url' => $preference['init_point'],
            'preference_id' => $preference['id'],
            'external_reference' => $preference['external_reference']
        ]);
    } else {
        throw new Exception('Error al crear la preferencia de pago');
    }
    
} catch (Exception $e) {
    error_log("Payment preference error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}

$conn->close();
?>

