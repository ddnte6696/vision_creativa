// js/producto.js - VERSIÓN FINAL AUDITADA

document.addEventListener('DOMContentLoaded', () => {
    let productoActual = null;
    const urlParams = new URLSearchParams(window.location.search);
    const productoId = urlParams.get('id');

    if (!productoId) {
        document.querySelector('.content-area').innerHTML = '<h1>Producto no especificado.</h1>';
        return;
    }

    const nombreEl = document.getElementById('product-name');

    async function cargarProducto() {
        try {
            const response = await fetch(`${baseURL}api/producto.php?id=${productoId}`);
            if (!response.ok) throw new Error(`Error del servidor: ${response.status}`);
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            productoActual = data;
            renderizarProducto(productoActual);
            setupAddToCartListener();
        } catch (error) {
            console.error("No se pudo cargar el producto:", error);
            if (nombreEl) nombreEl.textContent = "Error al cargar el producto";
            document.querySelector('.producto-detalles').innerHTML = `<h1>Producto no encontrado</h1><p>${error.message}</p>`;
        }
    }

    function renderizarProducto(data) {
        const descripcionEl = document.getElementById('product-description');
        const swatchesContainerEl = document.getElementById('color-swatches');
        const specsListEl = document.getElementById('product-specs-list');
        specsListEl.innerHTML = '';
        if (data.especificaciones && data.especificaciones.length > 0) {
            data.especificaciones.forEach(spec => {
                const valor = spec.valor || 'N/A';
                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>${spec.nombre_atributo}:</strong> <span>${valor}</span>`;
                specsListEl.appendChild(listItem);
            });
        }
        nombreEl.textContent = data.nombre;
        descripcionEl.textContent = data.descripcion;
        swatchesContainerEl.innerHTML = '';
        data.variaciones.forEach(variacion => {
            const swatch = document.createElement('button');
            swatch.classList.add('swatch');
            swatch.style.backgroundColor = variacion.color.codigo_hex;
            swatch.setAttribute('data-variacion-id', variacion.id_variacion);
            swatch.setAttribute('title', variacion.color.nombre);
            swatch.addEventListener('click', () => {
                actualizarVistaVariacion(variacion.id_variacion);
            });
            swatchesContainerEl.appendChild(swatch);
        });
        if (data.variaciones.length > 0) {
            actualizarVistaVariacion(data.variaciones[0].id_variacion);
        }
    }

    function actualizarVistaVariacion(variacionId) {
        const variacionSeleccionada = productoActual.variaciones.find(v => v.id_variacion === variacionId);
        if (!variacionSeleccionada) return;
        const imagenEl = document.getElementById('product-image');
        const precioEl = document.getElementById('product-price');
        const stockEl = document.getElementById('product-stock');
        const nombreColorEl = document.getElementById('color-name');
        imagenEl.src = variacionSeleccionada.url_imagen;
        imagenEl.alt = `${productoActual.nombre} en color ${variacionSeleccionada.color.nombre}`;
        precioEl.textContent = `$${variacionSeleccionada.precio_final.toFixed(2)}`;
        stockEl.textContent = `Disponibles: ${variacionSeleccionada.stock}`;
        nombreColorEl.textContent = variacionSeleccionada.color.nombre;
        document.querySelectorAll('.swatch').forEach(sw => {
            sw.classList.toggle('active', parseInt(sw.dataset.variacionId) === variacionId);
        });
        productoActual.selectedVariation = variacionSeleccionada;
    }

    function setupAddToCartListener() {
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const quantityInput = document.getElementById('quantity');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                if (!productoActual || !productoActual.selectedVariation) {
                    // --- ¡CORRECCIÓN CLAVE! ---
                    App.showToast('Selecciona un color primero', 'warning');
                    return;
                }
                if (!quantityInput.value || parseInt(quantityInput.value) < 1) {
                    // --- ¡CORRECCIÓN CLAVE! ---
                    App.showToast('Por favor, introduce una cantidad válida.', 'warning');
                    return;
                }
                const variation = productoActual.selectedVariation;
                const quantity = parseInt(quantityInput.value);
                if (typeof Cart !== 'undefined' && Cart.addToCart) {
                    Cart.addToCart(
                        productoActual.id,
                        variation.id_variacion,
                        productoActual.nombre + ` (${variation.color.nombre})`,
                        variation.precio_final,
                        variation.url_imagen,
                        quantity
                    );
                } else {
                    // --- ¡CORRECCIÓN CLAVE! ---
                    App.showToast('Error interno: El sistema de carrito no está disponible.', 'error');
                }
            });
        }
    }
    cargarProducto();
});