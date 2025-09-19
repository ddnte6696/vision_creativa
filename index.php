<?php 
session_start();
include 'templates/header.php'; 

?>

<main class="content-area">

    <!-- ================================================== -->
    <!-- 1. HERO BANNER (El Saludo del Imperio)             -->
    <!-- ================================================== -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Diseñados para tu Aventura Diaria</h1>
            <p>Calidad, estilo y funcionalidad en cada costura. Encuentra el compañero perfecto para tu viaje.</p>
            <a href="#product-grid-title" class="cta-button">Descubre la Colección</a>
        </div>
    </section>

    <!-- ================================================== -->
    <!-- 2. BANNER DE BENEFICIOS (Generador de Confianza)   -->
    <!-- ================================================== -->
    <section class="benefits-bar">
        <div class="benefit-item">
            <i class="bi bi-shield-check"></i>
            <span>Pago 100% Seguro</span>
        </div>
        <div class="benefit-item">
            <i class="bi bi-truck"></i>
            <span>Envíos a todo México</span>
        </div>
        <div class="benefit-item">
            <i class="bi bi-patch-check-fill"></i>
            <span>Garantía de Calidad</span>
        </div>
    </section>

    <!-- ================================================== -->
    <!-- 3. CUADRÍCULA DE PRODUCTOS (El Corazón del Catálogo) -->
    <!-- ================================================== -->
    <h2 id="product-grid-title" class="section-title">Nuestra Colección</h2>
    <div class="product-grid">
        <?php
        // La consulta se mantiene igual por ahora
        $sql = "SELECT 
                    p.id, 
                    p.nombre, 
                    p.precio_base,
                    (SELECT vp.url_imagen FROM variaciones_producto vp WHERE vp.id_producto = p.id ORDER BY vp.id LIMIT 1) AS imagen_principal
                FROM productos p
                WHERE p.activo = 1";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while($producto = $result->fetch_assoc()):
        ?>
                <a href="producto.php?id=<?php echo $producto['id']; ?>" class="product-card">
                    <div class="product-card-image">
                        <img src="<?php echo htmlspecialchars($producto['imagen_principal'] ?? 'img/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    </div>
                    <div class="product-card-info">
                        <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                        <p class="price">$<?php echo number_format($producto['precio_base'], 2); ?></p>
                    </div>
                </a>
        <?php 
            endwhile;
        else:
        ?>
            <p class="no-products">No hay productos disponibles en este momento.</p>
        <?php 
        endif;
        ?>
    </div>

    <!-- ================================================== -->
    <!-- 4. LLAMADA AL B2B (La Simbiosis en Acción)         -->
    <!-- ================================================== -->
    <section class="b2b-cta-banner">
        <div class="b2b-cta-content">
            <h2>¿Soluciones para tu Empresa?</h2>
            <p>Creamos productos personalizados que llevan tu marca al siguiente nivel. Descubre nuestro portal de co-creación.</p>
            <a href="b2b_nuevo_proyecto.php" class="cta-button-secondary">Visita La Fundición Creativa</a>
        </div>
    </section>

</main>

<?php include 'templates/footer.php'; ?>