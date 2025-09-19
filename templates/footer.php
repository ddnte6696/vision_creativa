<?php
// templates/footer.php
    // Cerramos la conexión a la base de datos al final de la vida de la página.
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
?>
            <footer class="main-footer">
                <p>&copy; 2025 Comercializadora Visión Creativa. Todos los derechos reservados.</p>
                <p>Av. de la Visión 123, Col. Creativa, Puebla, Pue. | Tel: (222) 123-4567 | contacto@visioncreativa.com</p>
            </footer>
        </div> <!-- Cierre de .main-wrapper -->
    </div> <!-- Cierre de .app-layout -->

    <!-- ================================================== -->
    <!-- Scripts Inteligentes y Dinámicos -->
    <!-- ================================================== -->
    
    <!-- Scripts Globales (se cargan en todas las páginas) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js" defer></script> <!-- ¡Añadido defer! -->
    <script src="js/cart.js" defer></script> <!-- ¡Añadido defer! -->
    <script src="js/address-manager.js" defer></script>
    <?php
    // Renderizamos los archivos CSS específicos que la página declaró
    // Se renderizan aquí al final para mayor especificidad sobre los estilos generales
    if (!empty($page_specific_assets['css'])) {
        foreach ($page_specific_assets['css'] as $css_file) {
            echo '<link rel="stylesheet" href="' . $css_file . '">';
        }
    }

    // Renderizamos los archivos JS específicos que la página declaró
    if (!empty($page_specific_assets['js'])) {
        foreach ($page_specific_assets['js'] as $js_file) {
            // ¡Añadimos defer para una carga más segura!
            echo '<script src="' . $js_file . '" defer></script>';
        }
    }
    ?>
    <!-- ================================================== -->
    <!-- EL PORTAL: Un universo separado para modales y toasts -->
    <!-- ================================================== -->
    <div id="portal-container"></div>
</body>
</html>