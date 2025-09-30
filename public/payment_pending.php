<?php
// public/payment_pending.php - Página de pago pendiente
session_start();
$pageTitle = 'Pago Pendiente - Vision Creativa';
include '../templates/header.php';
?>

<main class="content-area">
    <div class="container">
        <div class="payment-result pending">
            <div class="result-icon">
                <i class="fas fa-clock"></i>
            </div>
            
            <h1>Pago Pendiente</h1>
            <p class="lead">Tu pago está siendo procesado. Te notificaremos cuando sea confirmado.</p>
            
            <div class="pending-info">
                <h3>¿Qué está pasando?</h3>
                <ul>
                    <li> Tu pago está siendo verificado</li>
                    <li> Te enviaremos un email cuando se confirme</li>
                    <li> Puede tomar hasta 2 días hábiles</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="https://omnibus-guadalajara.com/vision_creativa/mi_cuenta.php" class="cta-button primary">Ver Estado</a>
                <a href="https://omnibus-guadalajara.com/vision_creativa/" class="cta-button secondary">Volver al Inicio</a>
            </div>
        </div>
    </div>
</main>

<style>
.pending .result-icon { color: #ffc107; }
.pending-info { margin: 2rem 0; background: #fff3cd; color: #856404; padding: 1.5rem; border-radius: 8px; }
</style>

<?php include '../templates/footer.php'; ?>
