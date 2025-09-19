<?php
// api/auth/login.php
session_start(); // Iniciamos la sesión
header('Content-Type: application/json');
require_once '../../db_config.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validación
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios.']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

try {
    $stmt = $conn->prepare("SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificamos la contraseña
        if (password_verify($password, $user['password_hash'])) {
            // ¡Contraseña correcta! Iniciamos la sesión.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_rol'] = $user['rol'];
            
            echo json_encode([
                'success' => true, 
                'message' => '¡Bienvenido de nuevo, ' . htmlspecialchars($user['nombre']) . '!'
            ]);

        } else {
            // Contraseña incorrecta
            http_response_code(401); // 401 Unauthorized
            echo json_encode(['success' => false, 'error' => 'La contraseña es incorrecta.']);
        }
    } else {
        // Usuario no encontrado
        http_response_code(404); // 404 Not Found
        echo json_encode(['success' => false, 'error' => 'No se encontró una cuenta con ese correo electrónico.']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>