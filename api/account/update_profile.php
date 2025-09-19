<?php
// api/account/update_profile.php
session_start();
header('Content-Type: application/json');
require_once '../../db_config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['nombre'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio.']);
    exit;
}

$nombre = $data['nombre'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
    $stmt->bind_param("si", $nombre, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_nombre'] = $nombre; // Actualizamos el nombre en la sesión
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado con éxito.']);
    } else {
        throw new Exception('No se pudo actualizar el perfil.');
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor.']);
}
?>