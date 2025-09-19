<?php 
// producto.php
session_start(); 
include 'templates/header.php'; 

// Esta página necesita su propio script de JS
$page_specific_assets['js'][] = 'js/producto.js';
?>

<main class="content-area">
    <div class="producto-container">
        <div class="producto-imagen">
            <img id="product-image" src="" alt="Imagen del producto">
        </div>
        <div class="producto-detalles">
            <h1 id="product-name">Cargando...</h1>
            
            <div class="product-info">
                <p id="product-description"></p>
            </div>

            <div class="product-specs">
                <h4>Especificaciones Técnicas</h4>
                <ul id="product-specs-list">
                    <!-- Las especificaciones se generarán aquí por JavaScript -->
                </ul>
            </div>

            <!-- Precio y Stock (Versión única y limpia) -->
            <div class="precio" id="product-price"></div>
            <div class="stock" id="product-stock"></div>

            <!-- Sección de Acción de Compra -->
            <div class="product-actions">
                <div class="quantity-selector">
                    <label for="quantity">Cantidad:</label>
                    <input type="number" id="quantity" value="1" min="1">
                </div>
                <button id="add-to-cart-btn" class="cta-button">Añadir al Carrito</button>
            </div>

            <!-- Selector de Color (Versión única y limpia) -->
            <div class="selector-color">
                <h3>Color: <span id="color-name"></span></h3>
                <div id="color-swatches" class="swatches-container">
                    <!-- Los swatches de color se generan aquí por JS -->
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>