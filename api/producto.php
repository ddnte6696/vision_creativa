<?php

// producto.php (API)
header('Content-Type: application/json');
require_once '../db_config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Se requiere un ID de producto válido.']);
    exit;
}
$producto_id = (int)$_GET['id'];

// --- CONSULTA 1: DATOS PRINCIPALES Y VARIACIONES ---
$sql_main = "SELECT p.*, vp.id AS variacion_id, vp.url_imagen, vp.stock, vp.precio_adicional,
             ac.id AS color_id, ac.nombre AS color_nombre, ac.codigo_hex
             FROM productos p
             LEFT JOIN variaciones_producto vp ON p.id = vp.id_producto
             LEFT JOIN atributos_color ac ON vp.id_color = ac.id
             WHERE p.id = ? AND p.activo = TRUE ORDER BY vp.id_color ASC";

$stmt_main = $conn->prepare($sql_main);
$stmt_main->bind_param("i", $producto_id);
$stmt_main->execute();
$result_main = $stmt_main->get_result();

if ($result_main->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Producto no encontrado.']);
    exit;
}

// --- NUEVA CONSULTA 2: ESPECIFICACIONES ---
$sql_specs = "SELECT asp.nombre_atributo, ps.valor
              FROM producto_specs ps
              JOIN atributos_spec asp ON ps.id_spec = asp.id
              WHERE ps.id_producto = ? ORDER BY asp.id ASC";

$stmt_specs = $conn->prepare($sql_specs);
$stmt_specs->bind_param("i", $producto_id);
$stmt_specs->execute();
$result_specs = $stmt_specs->get_result();

$especificaciones = [];
while ($row_spec = $result_specs->fetch_assoc()) {
    $especificaciones[] = $row_spec;
}
$stmt_specs->close();

// --- CONSTRUCCIÓN DEL JSON FINAL ---
$producto_data = [];
$primera_fila = true;
while ($row = $result_main->fetch_assoc()) {
    if ($primera_fila) {
        $producto_data = [
            'id' => (int)$row['id'], 'sku' => $row['sku'], 'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'], 'precio_base' => (float)$row['precio_base'],
            'especificaciones' => $especificaciones, // ¡Añadimos las especificaciones aquí!
            'variaciones' => []
        ];
        $primera_fila = false;
    }
    if ($row['variacion_id'] !== null) {
        $producto_data['variaciones'][] = [
            'id_variacion' => (int)$row['variacion_id'],
            'color' => ['id' => (int)$row['color_id'], 'nombre' => $row['color_nombre'], 'codigo_hex' => $row['codigo_hex']],
            'url_imagen' => $row['url_imagen'], 'stock' => (int)$row['stock'],
            'precio_final' => (float)$row['precio_base'] + (float)$row['precio_adicional']
        ];
    }
}
$stmt_main->close();
$conn->close();

echo json_encode($producto_data, JSON_PRETTY_PRINT);
?>