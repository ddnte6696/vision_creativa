<?php
// api/b2c/procesar_pedido.php
session_start();
header('Content-Type: application/json');
require_once '../../db_config.php';

// --- GUARDIA DE SEGURIDAD ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado.']);
    exit;
}

// --- OBTENCIÓN DE DATOS ---
// Los datos del pago (ID de transacción, estado, etc.) vendrán de la pasarela (ej. Mercado Pago).
// El carrito de compras lo obtendremos de la sesión o lo recibiremos del frontend.
$data = json_decode(file_get_contents('php://input'), true);
$payment_details = $data['paymentDetails'] ?? null; // Ejemplo de cómo recibiríamos los datos del pago
$cart_items = $data['cartItems'] ?? null; // El carrito final a procesar

if (!$payment_details || !$cart_items) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos de pago o del carrito.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$monto_total_pagado = 0; // Este valor debería venir de $payment_details

// --- INICIO DE LA TRANSACCIÓN DE BASE DE DATOS ---
// Esto es CRÍTICO. Si algo falla, se revierte todo.
$conn->begin_transaction();

try {
    // PASO 1: Validar el stock de cada producto en el carrito.
    foreach ($cart_items as $item) {
        $stmt_check = $conn->prepare("SELECT stock FROM variaciones_producto WHERE id = ? FOR UPDATE");
        $stmt_check->bind_param("i", $item['variacionId']);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $producto_db = $result->fetch_assoc();
        $stmt_check->close();

        if (!$producto_db || $producto_db['stock'] < $item['quantity']) {
            throw new Exception('Stock insuficiente para el producto: ' . htmlspecialchars($item['productName']));
        }
    }

    // PASO 2: Si el stock es suficiente, descontarlo.
    foreach ($cart_items as $item) {
        $stmt_update = $conn->prepare("UPDATE variaciones_producto SET stock = stock - ? WHERE id = ?");
        $stmt_update->bind_param("ii", $item['quantity'], $item['variacionId']);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // PASO 3: Crear el registro del pedido en la tabla `pedidos`.
    // Aquí se calcularía el monto total real desde la BD para seguridad.
    // Por ahora, usamos el que viene del pago.
    $stmt_pedido = $conn->prepare("INSERT INTO pedidos (id_usuario, tipo_pedido, monto_total, estado_pago, estado_envio) VALUES (?, 'b2c', ?, 'pagado_completo', 'preparando')");
    $stmt_pedido->bind_param("id", $user_id, $monto_total_pagado);
    $stmt_pedido->execute();
    $pedido_id = $conn->insert_id;
    $stmt_pedido->close();

    // (Opcional) Se podría crear una tabla `detalles_pedido` para guardar cada item del pedido.

    // Si todo fue exitoso, confirmamos la transacción.
    $conn->commit();

    // Limpiamos el carrito del lado del servidor si es necesario (ej. si lo guardamos en $_SESSION)

    echo json_encode(['success' => true, 'message' => '¡Pedido realizado con éxito!', 'orderId' => $pedido_id]);

} catch (Exception $e) {
    // Si algo falló, revertimos todos los cambios en la base de datos.
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>