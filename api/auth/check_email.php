<?php
// api/auth/check_email.php
header('Content-Type: application/json');
require_once '../../db_config.php';

if (!isset($_GET['email'])) {
    echo json_encode(['available' => false, 'message' => 'No se proporcionó email.']);
    exit;
}

$email = $_GET['email'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => 'Formato de email no válido.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['available' => false, 'message' => 'Este email ya está en uso.']);
    } else {
        echo json_encode(['available' => true, 'message' => 'Email disponible.']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['available' => false, 'message' => 'Error del servidor.']);
}
?>