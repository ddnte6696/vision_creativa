<?php 
session_start(); 
include 'templates/header.php'; 

$page_specific_assets['css'][] = 'css/auth.css';
$page_specific_assets['css'][] = 'css/components/forms.css';
$page_specific_assets['js'][] = 'js/auth.js';
?>

<main class="content-area">
    <div class="auth-container">
        <h1>Crear una Cuenta</h1>
        <p>Únete a la comunidad de Visión Creativa para una experiencia de compra más rápida.</p>
        
        <form id="register-form" class="auth-form" novalidate>
            <!-- Campo Honeypot: Trampa para bots -->
            <div class="form-group honeypot">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
                <small class="feedback-message" id="email-feedback"></small>
            </div>

            <div class="form-group password-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" id="toggle-password"><i class="bi bi-eye-slash"></i></button>
                <div class="password-strength-meter">
                    <div class="strength-bar" id="strength-bar"></div>
                </div>
                <small class="feedback-message" id="password-strength-text"></small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
                <small class="feedback-message" id="password-confirm-feedback"></small>
            </div>

            <div class="form-group-checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">Acepto los <a href="terminos.php" target="_blank">Términos y Condiciones</a> y la <a href="privacidad.php" target="_blank">Política de Privacidad</a>.</label>
            </div>

            <button type="submit" id="register-submit-btn" class="cta-button" disabled>Crear Cuenta</button>
        </form>
        
        <div class="auth-link">
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></p>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>