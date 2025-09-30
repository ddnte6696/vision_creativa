<?php
// public/payment_failure.php - Página de pago fallido
session_start();
$pageTitle = 'Error en el Pago - Vision Creativa';
include '../templates/header.php';
?>

<main class="content-area">
    <div class="container">
        <div class="payment-result failure">
            <div class="result-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            
            <h1>Error en el Pago</h1>
            <p class="lead">No pudimos procesar tu pago. Por favor, intenta nuevamente.</p>
            
            <div class="error-reasons">
                <h3>Posibles causas:</h3>
                <ul>
                    <li>Datos de tarjeta incorrectos</li>
                    <li>Fondos insuficientes</li>
                    <li>Problemas técnicos temporales</li>
                    <li>Transacción cancelada</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="https://omnibus-guadalajara.com/vision_creativa/checkout.php" class="cta-button primary">Reintentar Pago</a>
                <a href="https://omnibus-guadalajara.com/vision_creativa/carrito.php" class="cta-button secondary">Volver al Carrito</a>
            </div>
        </div>
    </div>
</main>

<style>
.failure .result-icon { color: #dc3545; }
.error-reasons { margin: 2rem 0; background: #f8d7da; color: #721c24; padding: 1.5rem; border-radius: 8px; }
</style>

<?php include '../templates/footer.php'; ?>
