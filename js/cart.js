// js/cart.js - VERSIÓN FINAL AUDITADA

const Cart = {
    init() { this.updateCartIcon(); },
    getCart() { return JSON.parse(localStorage.getItem('visionCreativaCart')) || []; },
    saveCart(cart) {
        localStorage.setItem('visionCreativaCart', JSON.stringify(cart));
        this.updateCartIcon();
        window.dispatchEvent(new Event('cartUpdated'));
    },
    addToCart(productId, variacionId, productName, price, image, quantity) {
        const cart = this.getCart();
        const existingItem = cart.find(item => item.variacionId === variacionId);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ productId, variacionId, productName, price, image, quantity });
        }
        this.saveCart(cart);
        // --- ¡LA CORRECCIÓN CLAVE! ---
        App.showToast(`${quantity} x "${productName}" añadido(s) al carrito!`);
    },
    updateQuantity(variacionId, newQuantity) {
        let cart = this.getCart();
        const item = cart.find(item => item.variacionId === variacionId);
        if (item) {
            if (newQuantity > 0) {
                item.quantity = newQuantity;
            } else {
                cart = cart.filter(item => item.variacionId !== variacionId);
            }
            this.saveCart(cart);
        }
    },
    removeFromCart(variacionId) {
        let cart = this.getCart();
        cart = cart.filter(item => item.variacionId !== variacionId);
        this.saveCart(cart);
    },
    updateCartIcon() {
        const cart = this.getCart();
        const cartBadge = document.getElementById('cart-count-badge');
        if (cartBadge) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'block' : 'none';
        }
    }
};
Cart.init();