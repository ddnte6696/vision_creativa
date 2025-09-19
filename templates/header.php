<?php
// templates/header.php
require_once 'db_config.php'; // Aseguramos que la config se carga primero

// Iniciamos un array para almacenar los scripts y estilos específicos de la página.
$page_specific_assets = ['css' => [], 'js' => []];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visión Creativa</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- ================================================== -->
    <!-- Definición de Variables Globales para JavaScript   -->
    <!-- ESTO ES CRUCIAL QUE ESTÉ AQUÍ, EN EL <HEAD>         -->
    <!-- ================================================== -->
    <script>
        const baseURL = "<?php echo rtrim(BASE_URL, '/'); ?>/";
    </script>
    <!-- ================================================== -->

</head>
<body>
    <!-- El backdrop para cerrar la sidebar -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo BASE_URL; ?>" title="Ir a la página principal">
                    <img src="img/logo.png" alt="Logo Visión Creativa" class="logo">
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#"><i class="bi bi-briefcase-fill"></i> Maletines y Mochilas</a></li>
                    <li><a href="#"><i class="bi bi-compass"></i> Accesorios de Viaje</a></li>
                    <li><a href="#"><i class="bi bi-cpu"></i> Tecnología</a></li>
                    <li><a href="#"><i class="bi bi-tag-fill"></i> Ofertas</a></li>
                    <li><a href="#"><i class="bi bi-envelope-fill"></i> Contacto</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="main-header">
                <div class="header-left">
                    <button class="menu-btn" id="open-sidebar-btn">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="brand-name">
                        <span>Comercializadora</span>
                        Visión Creativa
                    </h1>
                </div> 
                <div class="header-actions">
                    <a href="#" title="Buscar"><i class="bi bi-search"></i></a>
                    
                    <!-- ================================================== -->
                    <!-- Lógica Dinámica para el Enlace de Cuenta           -->
                    <!-- ================================================== -->
                    <?php
                    // La sesión ya fue iniciada por la página principal.
                    // Solo necesitamos comprobar si el usuario está logueado.
                    if (isset($_SESSION['user_id'])):
                    ?>
                        <a href="<?php echo BASE_URL; ?>mi_cuenta.php" title="Mi Cuenta">
                            <i class="bi bi-person-check-fill"></i> <!-- Icono de usuario logueado -->
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php" title="Iniciar Sesión / Registrarse">
                            <i class="bi bi-person-circle"></i> <!-- Icono de usuario no logueado -->
                        </a>
                    <?php endif; ?>
                    <!-- ================================================== -->
                    
                    <a href="<?php echo BASE_URL; ?>carrito.php" title="Ver Carrito"><i class="bi bi-cart3"></i> <span id="cart-count-badge" class="cart-count">0</span></a>
                </div>
            </header>