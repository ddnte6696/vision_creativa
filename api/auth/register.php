<?php
// api/auth/register.php
header('Content-Type: application/json');
require_once '../../db_config.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validación de datos
if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
    exit;
}
if ($data['password'] !== $data['password_confirm']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Las contraseñas no coinciden.']);
    exit;
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El formato del correo electrónico no es válido.']);
    exit;
}

$nombre = $data['nombre'];
$email = $data['email'];
$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
$rol = 'b2c_client'; // Todos los registros desde aquí son clientes B2C

try {
    // Verificar si el email ya existe
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        http_response_code(409); // 409 Conflict
        echo json_encode(['success' => false, 'error' => 'Este correo electrónico ya está registrado.']);
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();

    // Insertar nuevo usuario
    $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("ssss", $nombre, $email, $password_hash, $rol);
    
    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true, 'message' => '¡Registro exitoso! Ahora puedes iniciar sesión.']);
    } else {
        throw new Exception('No se pudo crear la cuenta.');
    }
    
    $stmt_insert->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>