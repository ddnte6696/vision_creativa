// js/address-manager.js - Lógica reutilizable para el formulario de dirección

function initializeAddressForm() {
    const addressFormContainer = document.getElementById('address-form-container');
    const addressForm = document.getElementById('address-form');
    const cancelAddressBtn = document.getElementById('cancel-address-btn');
    const toggleButtons = document.querySelectorAll('#add-address-btn, #toggle-address-form-btn');

    if (!addressFormContainer || !addressForm || !cancelAddressBtn || toggleButtons.length === 0) {
        return; // No hacer nada si los elementos no están en la página
    }

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            addressForm.reset();
            document.getElementById('address_id').value = '';
            addressFormContainer.style.display = 'block';
            btn.style.display = 'none';
        });
    });

    cancelAddressBtn.addEventListener('click', () => {
        addressFormContainer.style.display = 'none';
        toggleButtons.forEach(btn => btn.style.display = 'inline-block');
    });

    addressForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addressForm);
        const data = Object.fromEntries(formData.entries());
        try {
            const response = await fetch(`${baseURL}api/account/manage_address.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                App.showToast('Dirección guardada', 'success');
                // Recargamos la página para ver la nueva dirección en la lista
                window.location.reload();
            } else { throw new Error(result.error); }
        } catch (error) { App.showToast(error.message, 'error'); }
    });
}

// Llamamos a la función para que se active en cualquier página que la necesite
initializeAddressForm();