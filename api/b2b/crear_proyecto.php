<?php
// api/b2b/crear_proyecto.php
header('Content-Type: application/json');
require_once '../../db_config.php';

// Suponemos que el ID del cliente B2B está en la sesión una vez que inicie sesión.
// Por ahora, usaremos un ID de marcador de posición.
session_start();
$cliente_b2b_id = $_SESSION['user_id'] ?? 1; // ¡IMPORTANTE! Reemplazar con autenticación real.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// --- Validación Rigurosa de Datos ---
if (empty($data['requerimientos']) || empty($data['piezas'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Los requerimientos y la cantidad de piezas son obligatorios.']);
    exit;
}

// Sanitización
$requerimientos = htmlspecialchars($data['requerimientos']);
$especificaciones = htmlspecialchars($data['especificaciones'] ?? '');
$presupuesto = filter_var($data['presupuesto'], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
$piezas = filter_var($data['piezas'], FILTER_VALIDATE_INT);
$configuracion = $data['configuracion'] ?? [];
$id_color = filter_var($configuracion['colorId'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id_tela = filter_var($configuracion['telaId'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id_diseno = filter_var($configuracion['disenoId'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

$nombre_proyecto = "Proyecto Personalizado - " . date('Y-m-d H:i');

try {
    $conn->begin_transaction();

    // 1. Insertar en proyectos_b2b
    $stmt_proyecto = $conn->prepare(
        "INSERT INTO proyectos_b2b (id_cliente_b2b, nombre_proyecto, requerimientos, presupuesto, cantidad_piezas, estado) VALUES (?, ?, ?, ?, ?, 'propuesta')"
    );
    $stmt_proyecto->bind_param("issdi", $cliente_b2b_id, $nombre_proyecto, $requerimientos, $presupuesto, $piezas);
    $stmt_proyecto->execute();
    $proyecto_id = $conn->insert_id;
    $stmt_proyecto->close();

    // 2. Insertar en configuracion_proyecto_b2b
    $stmt_config = $conn->prepare(
        "INSERT INTO configuracion_proyecto_b2b (id_proyecto_b2b, id_color, id_tela, id_diseno_base) VALUES (?, ?, ?, ?)"
    );
    $stmt_config->bind_param("iiii", $proyecto_id, $id_color, $id_tela, $id_diseno);
    $stmt_config->execute();
    $stmt_config->close();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Propuesta enviada con éxito.', 'projectId' => $proyecto_id]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al crear el proyecto: ' . $e->getMessage()]);
}

$conn->close();
?>