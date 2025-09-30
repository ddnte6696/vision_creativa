// js/checkout.js - Actualizado con integración de Mercado Pago
document.addEventListener('DOMContentLoaded', () => {
    const summaryCartItemsEl = document.getElementById('summary-cart-items');
    const summaryTotalsEl = document.getElementById('summary-totals');
    const placeOrderBtn = document.getElementById('place-order-btn');
    
    let selectedShippingAddress = null;

    function renderSummary() {
        const cart = Cart.getCart();
        let html = '';
        let total = 0;

        if (cart.length === 0) {
            html = '<p class="empty-cart">Tu carrito está vacío</p>';
            summaryCartItemsEl.innerHTML = html;
            summaryTotalsEl.innerHTML = '';
            if (placeOrderBtn) placeOrderBtn.disabled = true;
            return;
        }

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            html += `
                <div class="summary-item">
                    <img src="${item.image}" alt="${item.productName}" class="item-image">
                    <div class="summary-item-details">
                        <p class="item-name"><strong>${item.productName}</strong></p>
                        <p class="item-quantity">Cantidad: ${item.quantity}  $${item.price.toFixed(2)}</p>
                        ${item.selectedOptions ? `<p class="item-options">${item.selectedOptions}</p>` : ''}
                    </div>
                    <div class="summary-item-total">$${subtotal.toFixed(2)}</div>
                </div>
            `;
        });

        summaryCartItemsEl.innerHTML = html;
        
        const shipping = 0;
        const tax = 0;
        const finalTotal = total + shipping + tax;

        summaryTotalsEl.innerHTML = `
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span>Envío:</span>
                <span class="free-shipping">Gratis</span>
            </div>
            <hr>
            <div class="summary-row total">
                <span><strong>Total:</strong></span>
                <span><strong>$${finalTotal.toFixed(2)}</strong></span>
            </div>
        `;
    }

    // Manejar selección de dirección de envío
    document.addEventListener('change', (e) => {
        if (e.target.name === 'shipping_address') {
            try {
                const addresses = JSON.parse(e.target.dataset.addresses || '[]');
                selectedShippingAddress = addresses[e.target.value];
                
                if (placeOrderBtn) {
                    const cart = Cart.getCart();
                    placeOrderBtn.disabled = !selectedShippingAddress || cart.length === 0;
                }
            } catch (error) {
                console.error('Error parsing addresses:', error);
                selectedShippingAddress = null;
                if (placeOrderBtn) placeOrderBtn.disabled = true;
            }
        }
    });

    // Manejar click del botón de pagar con Mercado Pago
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            // Validaciones
            if (!selectedShippingAddress) {
                App.showToast('Por favor selecciona una dirección de envío', 'warning');
                return;
            }

            const cart = Cart.getCart();
            if (cart.length === 0) {
                App.showToast('Tu carrito está vacío', 'warning');
                return;
            }

            // Mostrar loading
            placeOrderBtn.disabled = true;
            const originalText = placeOrderBtn.textContent;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                const response = await fetch('https://omnibus-guadalajara.com/vision_creativa/api/payments/create_preference.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        cartItems: cart,
                        shippingAddress: selectedShippingAddress
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    App.showToast('Redirigiendo a Mercado Pago...', 'success');
                    
                    // Redirigir a Mercado Pago
                    setTimeout(() => {
                        window.location.href = result.checkout_url;
                    }, 1000);
                    
                } else {
                    throw new Error(result.error || 'Error desconocido');
                }

            } catch (error) {
                console.error('Payment error:', error);
                App.showToast(`Error: ${error.message}`, 'error');
                
                // Restaurar botón
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = originalText;
            }
        });
    }

    // Renderizar resumen inicial
    renderSummary();
    
    // Escuchar cambios en el carrito
    document.addEventListener('cartUpdated', renderSummary);
    
    // Estado inicial del botón
    if (placeOrderBtn) {
        const cart = Cart.getCart();
        placeOrderBtn.disabled = cart.length === 0 || !selectedShippingAddress;
    }
});
