// js/pagina-carrito.js - Lógica para la página de visualización del carrito (VERSIÓN FINAL AUDITADA)

document.addEventListener('DOMContentLoaded', () => {

    const cartContainer = document.getElementById('cart-container');

    function renderCartPage() {
        const cart = Cart.getCart();

        if (cart.length === 0) {
            cartContainer.innerHTML = `
                <div class="cart-empty">
                    <p>Tu carrito está vacío.</p>
                    <!-- ¡ASEGURAMOS QUE LA CLASE cta-button ESTÉ AQUÍ! -->
                    <a href="index.php" class="cta-button">Continuar Comprando</a>
                </div>
            `;
            return;
        }

        let subtotal = 0;
        const itemsHtml = cart.map(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            return `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.productName}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h3>${item.productName}</h3>
                        <p>Precio: $${item.price.toFixed(2)}</p>
                    </div>
                    <div class="cart-item-quantity">
                        <input type="number" value="${item.quantity}" min="1" class="quantity-input" data-variacion-id="${item.variacionId}">
                    </div>
                    <div class="cart-item-total">
                        $${itemTotal.toFixed(2)}
                    </div>
                    <div class="cart-item-remove">
                        <button class="remove-btn" data-variacion-id="${item.variacionId}" title="Eliminar producto">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        const summaryHtml = `
            <div class="cart-summary">
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
                <!-- ¡ASEGURAMOS QUE LA CLASE cta-button ESTÉ AQUÍ! -->
                <a href="checkout.php" class="cta-button checkout-btn">Proceder al Pago</a>
            </div>
        `;

        cartContainer.innerHTML = `<div class="cart-items-list">${itemsHtml}</div>${summaryHtml}`;
        addCartPageListeners();
    }

    function addCartPageListeners() {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const variacionId = parseInt(e.target.dataset.variacionId);
                const newQuantity = parseInt(e.target.value);
                Cart.updateQuantity(variacionId, newQuantity);
            });
        });

        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const variacionId = parseInt(e.currentTarget.dataset.variacionId);
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--azul-vw)',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, ¡eliminar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Cart.removeFromCart(variacionId);
                        App.showToast('Producto eliminado', 'success');
                    }
                });
            });
        });
    }

    // Escuchamos el evento global 'cartUpdated' para re-renderizar la página si algo cambia
    window.addEventListener('cartUpdated', renderCartPage);

    // Renderizamos el carrito por primera vez al cargar la página
    renderCartPage();
});