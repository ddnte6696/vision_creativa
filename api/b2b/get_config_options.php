<?php
// api/b2b/get_config_options.php
header('Content-Type: application/json');
require_once '../../db_config.php'; // Ajusta la ruta según tu estructura

try {
    $conn->begin_transaction();

    // Obtener Colores
    $colores = $conn->query("SELECT id, nombre, codigo_hex FROM atributos_color ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

    // Obtener Telas
    $telas = $conn->query("SELECT id, nombre, descripcion, url_textura FROM atributos_tela ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

    // Obtener Diseños Base
    $disenos = $conn->query("SELECT id, nombre, descripcion, url_imagen_plantilla FROM disenos_base ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'data' => [
            'colores' => $colores,
            'telas' => $telas,
            'disenos' => $disenos
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener las opciones de configuración: ' . $e->getMessage()]);
}

$conn->close();
?>