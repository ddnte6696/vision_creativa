// js/checkout.js - Lógica para la página de Checkout (Versión Refactorizada)

document.addEventListener('DOMContentLoaded', () => {
    const summaryCartItemsEl = document.getElementById('summary-cart-items');
    const summaryTotalsEl = document.getElementById('summary-totals');
    const placeOrderBtn = document.getElementById('place-order-btn');

    function renderSummary() {
        const cart = Cart.getCart();
        if (cart.length === 0 && summaryCartItemsEl) {
            // Si el carrito está vacío, no hay nada que pagar. Redirigimos.
            window.location.href = 'carrito.php';
            return;
        }

        if (summaryCartItemsEl) {
            summaryCartItemsEl.innerHTML = cart.map(item => `
                <div class="summary-cart-item">
                    <img src="${item.image}" alt="${item.productName}">
                    <div class="summary-item-details">
                        <p><strong>${item.productName}</strong></p>
                        <p>${item.quantity} x $${item.price.toFixed(2)}</p>
                    </div>
                </div>
            `).join('');
        }

        if (summaryTotalsEl) {
            let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let iva = subtotal * 0.16;
            let total = subtotal + iva;

            summaryTotalsEl.innerHTML = `
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$${subtotal.toFixed(2)}</span>
                </div>
                <div class="summary-row">
                    <span>IVA (16%):</span>
                    <span>$${iva.toFixed(2)}</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$${total.toFixed(2)}</span>
                </div>
            `;
        }
    }

    // La lógica para mostrar/ocultar el formulario de dirección
    // ha sido movida a js/address-manager.js para ser reutilizable.

    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', () => {
            // Punto de entrada para la lógica de pago del otro desarrollador.
            App.showToast('Procediendo al pago...', 'info');
            // Aquí se llamaría a la función de Mercado Pago.
        });
    }

    // Renderizamos el resumen al cargar la página
    renderSummary();
});