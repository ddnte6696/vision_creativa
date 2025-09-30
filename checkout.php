<?php
session_start();

// --- GUARDIA DE SEGURIDAD ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout'); // Lo mandamos al login, y le decimos a dónde volver
    exit;
}

include 'templates/header.php'; 

$page_specific_assets['css'][] = 'css/checkout.css';
$page_specific_assets['css'][] = 'css/components/forms.css';
$page_specific_assets['js'][] = 'js/checkout.js';

// --- RECUPERAR DATOS DEL USUARIO Y DIRECCIONES ---
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
    <div class="checkout-container">
        <h1>Finalizar Compra</h1>

        <div class="checkout-grid">
            <!-- Columna Izquierda: Detalles de Envío y Pago -->
            <div class="checkout-details">
                
                <!-- SECCIÓN DE DIRECCIÓN DE ENVÍO -->
                <section class="checkout-section">
                    <h2>1. Dirección de Envío</h2>
                    <div id="address-selection-area">
                        <?php if (!empty($user_data['direcciones'])): ?>
                            <p>Selecciona una dirección guardada:</p>
                            <div class="address-options">
                                <?php foreach ($user_data['direcciones'] as $index => $addr): ?>
                                    <div class="address-option">
                                        <input type="radio" name="selected_address" id="addr_<?php echo $addr['id']; ?>" value="<?php echo $addr['id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                        <label for="addr_<?php echo $addr['id']; ?>">
                                            <strong><?php echo htmlspecialchars($addr['calle']); ?></strong><br>
                                            <?php echo htmlspecialchars($addr['colonia']); ?><br>
                                            <?php echo htmlspecialchars($addr['ciudad']); ?>, <?php echo htmlspecialchars($addr['estado']); ?>, C.P. <?php echo htmlspecialchars($addr['cp']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No tienes direcciones guardadas. Por favor, añade una.</p>
                        <?php endif; ?>
                        <button id="toggle-address-form-btn" class="cta-button-secondary">Añadir Nueva Dirección</button>
                    </div>
                    
    
                    <!-- Formulario para añadir dirección (oculto por defecto) -->
                    <div id="address-form-container" style="display: none;">
                        <?php include 'templates/address_form.php'; ?>
                    </div>
                </section>

                <!-- SECCIÓN DE MÉTODO DE PAGO -->
                <section class="checkout-section">
                    <h2>2. Método de Pago</h2>
                    <div class="payment-section">
                        <!-- MERCADO PAGO INTEGRATION -->
                        <div class="payment-method mp-payment">
                            <div class="payment-method-header">
                                <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/logo__large.png" 
                                     alt="Mercado Pago" class="mp-logo">
                                <h3>Paga con Mercado Pago</h3>
                            </div>
                            <p class="payment-description">
                                Paga de forma segura con tarjeta de crédito, débito, transferencia o efectivo.
                            </p>
                            

                            <!-- BOTÓN PRINCIPAL DE MERCADO PAGO -->
                            <button id="mp-checkout-btn" class="mp-button">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-10-5z"/>
                                    <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" fill="none"/>
                                </svg>
                                Pagar con Mercado Pago
                            </button>
                            
                            <!-- INDICADORES DE MÉTODOS DE PAGO DISPONIBLES -->
                            <div class="payment-methods-icons">
                                <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/visa.png" alt="Visa" title="Visa">
                                <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/mastercard.png" alt="Mastercard" title="Mastercard">
                                <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/amex.png" alt="American Express" title="American Express">
                                <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/oxxo.png" alt="OXXO" title="OXXO">
                            </div>
                            
                            <!-- MENSAJES DE ESTADO -->
                            <div id="payment-status" class="payment-status" style="display: none;">
                                <div class="status-message"></div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Columna Derecha: Resumen del Pedido -->
            <aside class="order-summary">
                <h2>Resumen de tu Pedido</h2>
                <div id="summary-cart-items">
                    <!-- Los items del carrito se cargarán aquí con JS -->
                </div>
                <div id="summary-totals">
                    <!-- Los totales se cargarán aquí con JS -->
                </div>
                <button id="place-order-btn" class="cta-button">Realizar Pedido</button>
            </aside>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>