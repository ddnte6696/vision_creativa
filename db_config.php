<?php
// db_config.php

// --- DETECCIÓN AUTOMÁTICA DE LA URL BASE (Versión Definitiva) ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// Esta lógica asume que db_config.php está en la raíz del proyecto.
// Si lo movieras a una subcarpeta /config, necesitaríamos ajustar la ruta.
// Por ahora, con nuestra estructura actual, esto es perfecto.
define('BASE_URL', $protocol . $host . $script_dir);


// --- CONEXIÓN A LA BASE DE DATOS ---
$servername = "localhost";
$username = "root"; // Reemplaza con tu usuario
$password = ""; // Reemplaza con tu contraseña
$dbname = "diginet_vision_creativa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el charset a UTF-8 para una correcta codificación
$conn->set_charset("utf8mb4");

// NOTA IMPORTANTE: No hay etiqueta de cierre "?>