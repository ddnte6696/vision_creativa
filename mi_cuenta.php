<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'templates/header.php'; 

$page_specific_assets['css'][] = 'css/account.css';
$page_specific_assets['css'][] = 'css/components/forms.css';
$page_specific_assets['js'][] = 'js/account.js';

// ... (La lógica de PHP para obtener $user_data se queda exactamente igual) ...
require_once 'db_config.php';
$user_id = $_SESSION['user_id'];
$user_data = null;
$error_message = '';
if (isset($conn) && $conn instanceof mysqli) {
    try {
        $stmt = $conn->prepare("SELECT nombre, email, datos_envio FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user_data = $result->fetch_assoc();
            $user_data['direcciones'] = $user_data['datos_envio'] ? json_decode($user_data['datos_envio'], true) : [];
        }
        $stmt->close();
    } catch (Exception $e) { $error_message = "Error al cargar los datos del usuario."; }
} else { $error_message = "No se pudo establecer la conexión con la base de datos."; }
?>

<main class="content-area">
    <div class="account-container">
        <h1>Mi Cuenta</h1>
        
        <!-- ================================================== -->
        <!-- NUEVA ESTRUCTURA DE PESTAÑAS (TABS)                -->
        <!-- ================================================== -->
        <nav class="account-tabs">
            <ul>
                <li class="active"><a href="#perfil"><i class="bi bi-person-fill"></i> Mi Perfil</a></li>
                <li><a href="#direcciones"><i class="bi bi-geo-alt-fill"></i> Mis Direcciones</a></li>
                <li><a href="#pedidos"><i class="bi bi-box-seam"></i> Mis Pedidos</a></li>
                <li><a href="api/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>

        <!-- Contenido Principal de la Cuenta -->
        <div class="account-content-wrapper">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php else: ?>
                <!-- SECCIÓN: MI PERFIL -->
                <div id="perfil" class="account-section">
                    <!-- El contenido del perfil se queda igual -->
                    <h2>Mi Perfil</h2>
                    <form id="profile-form">
                        <div class="form-group"><label for="profile-nombre">Nombre Completo</label><input type="text" id="profile-nombre" name="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" required></div>
                        <div class="form-group"><label for="profile-email">Correo Electrónico</label><input type="email" id="profile-email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled><small>El correo electrónico no se puede cambiar.</small></div>
                        <button type="submit" class="cta-button">Guardar Cambios</button>
                    </form>
                </div>

                <!-- SECCIÓN: MIS DIRECCIONES -->
                <div id="direcciones" class="account-section" style="display: none;">
                    <!-- El contenido de las direcciones se queda igual -->
                    <h2>Mis Direcciones</h2>
                    <div id="address-list"></div>
                    <button id="add-address-btn" class="cta-button-secondary">Añadir Nueva Dirección</button>
                    <div id="address-form-container" style="display: none;">
                        <?php include 'templates/address_form.php'; ?>
                    </div>
                </div>

                <!-- SECCIÓN: MIS PEDIDOS -->
                <div id="pedidos" class="account-section" style="display: none;">
                    <h2>Mis Pedidos</h2>
                    <p>Aquí verás el historial de tus compras.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>