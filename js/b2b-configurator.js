// js/b2b-configurator.js - Lógica para el Portal de Co-Creación B2B

document.addEventListener('DOMContentLoaded', () => {
    // --- ESTADO DE LA CONFIGURACIÓN ---
    const configuracionActual = {
        colorId: null,
        telaId: null,
        disenoId: null,
    };
    let configuradorData = null; // Guardaremos aquí los datos de la API (colores, telas, etc.)

    // --- ELEMENTOS DEL DOM ---
    const formEl = document.getElementById('b2b-project-form');
    const configuratorAreaEl = document.getElementById('b2b-configurator-interactive-area');

    // --- FUNCIÓN DE INICIALIZACIÓN ---
    async function init() {
        try {
            const response = await fetch(`${baseURL}api/b2b/get_config_options.php`);
            if (!response.ok) throw new Error('No se pudieron cargar las opciones.');
            const result = await response.json();
            if (result.success) {
                configuradorData = result.data; // Guardamos los datos para usarlos después
                renderConfigurator(configuradorData);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            configuratorAreaEl.innerHTML = `<p class="error-placeholder">Error: ${error.message}</p>`;
        }
    }

    // --- FUNCIÓN PARA RENDERIZAR EL CONFIGURADOR COMPLETO ---
    function renderConfigurator(options) {
        const previewHtml = `
            <div class="configurator-preview">
                <h4>Previsualización Dinámica</h4>
                <div id="preview-box" class="preview-box">
                    <!-- Capa 1: Fondo para color y textura -->
                    <div id="preview-background" class="preview-layer"></div>
                    <!-- Capa 2: Imagen del diseño (PNG transparente) -->
                    <img id="preview-design-layer" class="preview-layer" src="img/designs/placeholder.png" alt="Previsualización del producto">
                    <p id="preview-text">Selecciona un diseño base para comenzar</p>
                </div>
            </div>
        `;

        configuratorAreaEl.innerHTML = `
            <div class="configurator-grid">
                <div class="configurator-options">
                    ${renderSelector('Color', options.colores, 'color')}
                    ${renderSelector('Material / Tela', options.telas, 'tela')}
                    ${renderSelector('Diseño Base', options.disenos, 'diseno')}
                </div>
                ${previewHtml}
            </div>
        `;
        addEventListeners();
    }
    
    // --- FUNCIÓN GENÉRICA PARA RENDERIZAR CADA SECCIÓN DE OPCIONES ---
function renderSelector(title, items, type) {
    let itemsHtml = items.map(item => {
        let visualContent = '';
        let textContent = '';
        let styleAttribute = '';

        if (type === 'color') {
            styleAttribute = `style="background-color: ${item.codigo_hex};"`;
            // No hay contenido de imagen ni de texto para los colores
        } else {
            visualContent = `<img src="${item.url_textura || item.url_imagen_plantilla}" alt="${item.nombre}">`;
            textContent = `<span>${item.nombre}</span>`;
        }

        return `
            <div class="option-card" data-type="${type}" data-id="${item.id}" title="${item.descripcion || item.nombre}">
                <div class="option-visual" ${styleAttribute}>${visualContent}</div>
                ${textContent}
            </div>
        `;
    }).join('');

    return `
        <div class="config-section">
            <h5>${title}</h5>
            <div class="options-container">${itemsHtml}</div>
        </div>
    `;
}
    // --- FUNCIÓN PARA AÑADIR LOS EVENT LISTENERS A LAS OPCIONES ---
    function addEventListeners() {
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('click', () => {
                const type = card.dataset.type;
                const id = parseInt(card.dataset.id);
                
                // Actualizar estado
                configuracionActual[`${type}Id`] = id;

                // Actualizar UI (marcar como activo)
                document.querySelectorAll(`.option-card[data-type="${type}"]`).forEach(c => c.classList.remove('active'));
                card.classList.add('active');

                updatePreview();
            });
        });
    }

    // --- FUNCIÓN PARA ACTUALIZAR LA VISTA PREVIA (VERSIÓN CON CAPAS) ---
    function updatePreview() {
        if (!configuradorData) return; // No hacer nada si los datos no han cargado

        const backgroundEl = document.getElementById('preview-background');
        const designLayerEl = document.getElementById('preview-design-layer');
        const previewTextEl = document.getElementById('preview-text');

        // 1. Manejar el Color
        if (configuracionActual.colorId) {
            const selectedColor = configuradorData.colores.find(c => c.id == configuracionActual.colorId);
            backgroundEl.style.backgroundColor = selectedColor ? selectedColor.codigo_hex : 'transparent';
        } else {
            backgroundEl.style.backgroundColor = 'transparent';
        }

        // 2. Manejar la Textura
        if (configuracionActual.telaId) {
            const selectedTela = configuradorData.telas.find(t => t.id == configuracionActual.telaId);
            backgroundEl.style.backgroundImage = selectedTela ? `url(${selectedTela.url_textura})` : 'none';
        } else {
            backgroundEl.style.backgroundImage = 'none';
        }

        // 3. Manejar el Diseño Base
        if (configuracionActual.disenoId) {
            const selectedDesign = configuradorData.disenos.find(d => d.id == configuracionActual.disenoId);
            if (selectedDesign) {
                designLayerEl.src = selectedDesign.url_imagen_plantilla;
                previewTextEl.style.display = 'none';
                //designLayerEl.style.display = 'block';
            }
                        // --- ¡AQUÍ ESTÁ LA NUEVA MAGIA! ---
            // Usamos la misma imagen del diseño como una máscara para el fondo.
            backgroundEl.style.maskImage = `url(${selectedDesign.url_imagen_plantilla})`;
            backgroundEl.style.webkitMaskImage = `url(${selectedDesign.url_imagen_plantilla})`; // Para compatibilidad con Chrome/Safari
            // ------------------------------------
        } else {
            // Si no hay diseño, mostramos el placeholder
            designLayerEl.src = 'img/designs/placeholder.png';
            previewTextEl.style.display = 'block';
            //designLayerEl.style.display = 'block'; // Mostramos el placeholder
        }
    }

    // --- MANEJO DEL ENVÍO DEL FORMULARIO ---
    formEl.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = formEl.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando propuesta...';

        const formData = new FormData(formEl);
        const projectData = {
            requerimientos: formData.get('requerimientos'),
            especificaciones: formData.get('especificaciones'),
            presupuesto: parseFloat(formData.get('presupuesto')),
            piezas: parseInt(formData.get('piezas')),
            configuracion: configuracionActual
        };

        try {
            const response = await fetch(`${baseURL}api/b2b/crear_proyecto.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
                // Redirigir a la página del proyecto recién creado
                window.location.href = `b2b_proyecto_detalle.php?id=${result.projectId}`;
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            alert(`Error al enviar la propuesta: ${error.message}`);
            submitButton.disabled = false;
            submitButton.textContent = 'Generar Propuesta Inicial y Cotizar';
        }
    });

    // --- INICIAMOS TODO ---
    init();
});