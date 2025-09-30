<?php
// public/payment_success.php - Página de pago exitoso
session_start();
require_once '../db_config.php';

$pageTitle = 'Pago Exitoso - Vision Creativa';
$externalRef = $_GET['ref'] ?? '';

include '../templates/header.php';
?>

<main class="content-area">
    <div class="container">
        <div class="payment-result success">
            <div class="result-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>¡Pago Exitoso!</h1>
            <p class="lead">Tu pedido ha sido procesado correctamente.</p>
            
            <?php if ($externalRef): ?>
            <div class="payment-details">
                <div class="detail-item">
                    <span class="label">Referencia:</span>
                    <span class="value"><?php echo htmlspecialchars($externalRef); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="next-steps">
                <h3>¿Qué sigue?</h3>
                <ul>
                    <li> Recibirás un email de confirmación</li>
                    <li> Tu pedido será procesado en 1-2 días hábiles</li>
                    <li> Te notificaremos cuando sea enviado</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="https://omnibus-guadalajara.com/vision_creativa/" class="cta-button primary">Volver al Inicio</a>
                <a href="https://omnibus-guadalajara.com/vision_creativa/mi_cuenta.php" class="cta-button secondary">Ver Mis Pedidos</a>
            </div>
        </div>
    </div>
</main>

<style>
.payment-result { max-width: 600px; margin: 0 auto; text-align: center; padding: 2rem; }
.result-icon { font-size: 4rem; margin-bottom: 1rem; }
.success .result-icon { color: #28a745; }
.payment-details { margin: 2rem 0; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; }
.detail-item { display: flex; justify-content: space-between; padding: 0.5rem 0; }
.next-steps ul { text-align: left; max-width: 400px; margin: 0 auto; }
.action-buttons .cta-button { margin: 0 0.5rem; }
</style>

<?php
include '../templates/footer.php';

// Limpiar carrito después del éxito
if (isset($_SESSION['cart_backup'])) {
    unset($_SESSION['cart_backup']);
    unset($_SESSION['payment_reference']);
}
?>
