<?php 
include 'templates/header.php'; 

// Esta página necesita su propio CSS y JS
$page_specific_assets['css'][] = 'css/b2b-creator.css';
$page_specific_assets['js'][] = 'js/b2b-configurator.js';
?>

<main class="content-area">
    <div class="b2b-creator-container">
        <div class="b2b-header">
            <h1>Portal de Co-Creación: La Fundición Creativa</h1>
            <p>Bienvenido a nuestro taller de ideas. Describe tu necesidad y utiliza nuestro configurador para dar vida al producto perfecto para tu empresa.</p>
        </div>

        <form id="b2b-project-form" class="b2b-form">
            
            <!-- SECCIÓN 1: REQUERIMIENTOS INICIALES -->
            <fieldset class="form-section">
                <legend>1. Cuéntanos tu Visión</legend>
                
                <div class="form-group">
                    <label for="requerimientos">¿Qué desea el cliente o cuál es su necesidad?</label>
                    <textarea id="requerimientos" name="requerimientos" rows="4" placeholder="Ej: Necesitamos una mochila resistente para nuestro equipo de ventas, que pueda llevar una laptop, catálogos y tenga el logo de nuestra empresa." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="especificaciones">Especificaciones técnicas requeridas y para qué uso</label>
                    <textarea id="especificaciones" name="especificaciones" rows="4" placeholder="Ej: Material impermeable, compartimento para laptop de 15.6 pulgadas, bolsillo frontal de acceso rápido, espalda acolchada."></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="presupuesto">Presupuesto estimado por unidad (MXN)</label>
                        <input type="number" id="presupuesto" name="presupuesto" min="0" step="0.01" placeholder="Ej: 550.00">
                    </div>
                    <div class="form-group">
                        <label for="piezas">¿Cuántas piezas requiere?</label>
                        <input type="number" id="piezas" name="piezas" min="1" placeholder="Ej: 500" required>
                    </div>
                </div>
            </fieldset>

            <!-- SECCIÓN 2: CONFIGURADOR INTERACTIVO (PLACEHOLDERS PARA JS) -->
            <fieldset class="form-section">
                <legend>2. Configura tu Producto</legend>
                
                <!-- Aquí inyectaremos dinámicamente el configurador con JS -->
                <div id="b2b-configurator-interactive-area">
                    <p class="loading-placeholder">Cargando opciones de configuración...</p>
                </div>

            </fieldset>

            <!-- SECCIÓN 3: MARCO LEGAL Y ENVÍO -->
            <fieldset class="form-section">
                <legend>3. Acuerdos y Siguientes Pasos</legend>

                <div class="legal-notice">
                    <h4>Limitaciones de Diseño y Propiedad Intelectual</h4>
                    <p>En "Visión Creativa", respetamos la propiedad intelectual. No se replicarán diseños, logos o marcas registradas existentes sin la debida autorización del titular. Todas las propuestas están sujetas a una revisión de viabilidad y legalidad.</p>
                </div>

                <div class="form-group-checkbox">
                    <input type="checkbox" id="legal-accept" name="legal-accept" required>
                    <label for="legal-accept">He leído y acepto los términos sobre propiedad intelectual.</label>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn-submit-b2b">Generar Propuesta Inicial y Cotizar</button>
            </div>

        </form>
    </div>
</main>

<?php include 'templates/footer.php'; ?>