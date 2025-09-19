<?php 
session_start(); 
include 'templates/header.php'; 

// Esta página necesita su propio CSS y JS
$page_specific_assets['css'][] = 'css/cart.css';
$page_specific_assets['js'][] = 'js/pagina-carrito.js';
?>

<main class="content-area">
    <div class="cart-page-container">
        <h1>Tu Carrito de Compras</h1>
        
        <!-- El contenido del carrito será renderizado aquí por JavaScript -->
        <div id="cart-container">
            <p class="loading-placeholder">Cargando tu carrito...</p>
        </div>

    </div>
</main>

<?php include 'templates/footer.php'; ?>