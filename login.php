<?php
session_start();  
include 'templates/header.php'; 

// Reutilizamos los mismos estilos y scripts de la página de registro
$page_specific_assets['css'][] = 'css/auth.css';
$page_specific_assets['css'][] = 'css/components/forms.css';
$page_specific_assets['js'][] = 'js/auth.js';
?>

<main class="content-area">
    <div class="auth-container">
        <h1>Iniciar Sesión</h1>
        <p>Bienvenido de nuevo a Visión Creativa.</p>
        
        <form id="login-form" class="auth-form" novalidate>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group password-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" id="toggle-password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <button type="submit" class="cta-button">Ingresar</button>
        </form>
        
        <div class="auth-link">
            <p>¿No tienes una cuenta? <a href="registro.php">Crea una aquí</a></p>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>