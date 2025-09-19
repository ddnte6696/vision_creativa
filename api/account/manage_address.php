<?php
// api/account/manage_address.php (Versión Robusta y Corregida)
session_start();
header('Content-Type: application/json');
require_once '../../db_config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Primero, obtenemos las direcciones actuales del usuario
    $stmt_get = $conn->prepare("SELECT datos_envio FROM usuarios WHERE id = ?");
    $stmt_get->bind_param("i", $user_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $user = $result->fetch_assoc();
    $direcciones = $user['datos_envio'] ? json_decode($user['datos_envio'], true) : [];
    $stmt_get->close();

    // --- ¡LÓGICA CORREGIDA Y EXPLÍCITA! ---

    // Lógica para GUARDAR (Crear o Actualizar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $is_update = !empty($data['address_id']);
        
        if ($is_update) {
            // --- CAMINO DE ACTUALIZACIÓN ---
            $address_id = $data['address_id'];
            $address_found = false;
            foreach ($direcciones as $key => $dir) {
                if ($dir['id'] === $address_id) {
                    $direcciones[$key]['calle'] = $data['calle'];
                    $direcciones[$key]['colonia'] = $data['colonia'];
                    $direcciones[$key]['ciudad'] = $data['ciudad'];
                    $direcciones[$key]['estado'] = $data['estado'];
                    $direcciones[$key]['cp'] = $data['cp'];
                    $direcciones[$key]['referencias'] = $data['referencias'] ?? '';
                    $address_found = true;
                    break;
                }
            }
            if (!$address_found) {
                throw new Exception('No se encontró la dirección para actualizar.');
            }
        } else {
            // --- CAMINO DE CREACIÓN ---
            $nueva_direccion = [
                'id' => uniqid('addr_'), // Generamos un ID único y nuevo
                'calle' => $data['calle'],
                'colonia' => $data['colonia'],
                'ciudad' => $data['ciudad'],
                'estado' => $data['estado'],
                'cp' => $data['cp'],
                'referencias' => $data['referencias'] ?? ''
            ];
            $direcciones[] = $nueva_direccion;
        }
    }

    // Lógica para ELIMINAR (se mantiene igual)
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $address_id_to_delete = $data['address_id'];
        $direcciones = array_filter($direcciones, function($dir) use ($address_id_to_delete) {
            return $dir['id'] !== $address_id_to_delete;
        });
        $direcciones = array_values($direcciones);
    }

    // Guardar el array de direcciones actualizado en la base de datos
    $json_direcciones = json_encode($direcciones);
    $stmt_update = $conn->prepare("UPDATE usuarios SET datos_envio = ? WHERE id = ?");
    $stmt_update->bind_param("si", $json_direcciones, $user_id);
    $stmt_update->execute();
    $stmt_update->close();

    echo json_encode(['success' => true, 'direcciones' => $direcciones]);
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>