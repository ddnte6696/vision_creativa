// js/account.js - Lógica para la página "Mi Cuenta" (Versión Refactorizada)

document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.account-tabs a');
    const sections = document.querySelectorAll('.account-section');
    
    // --- NAVEGACIÓN POR PESTAÑAS ---
    function setupTabs() {
        navLinks.forEach(link => {
            if (link.href.includes('#')) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    sections.forEach(section => {
                        section.style.display = section.id === targetId ? 'block' : 'none';
                    });
                    navLinks.forEach(navLink => navLink.parentElement.classList.remove('active'));
                    link.parentElement.classList.add('active');
                });
            }
        });
    }

    // --- LÓGICA DE FORMULARIO DE PERFIL ---
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const nombre = document.getElementById('profile-nombre').value;
            try {
                const response = await fetch(`${baseURL}api/account/update_profile.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre })
                });
                const result = await response.json();
                if (response.ok) {
                    App.showToast(result.message, 'success');
                } else { throw new Error(result.error); }
            } catch (error) {
                App.showToast(error.message, 'error');
            }
        });
    }

    // --- LÓGICA DE VISUALIZACIÓN DE DIRECCIONES ---
    const addressList = document.getElementById('address-list');
    const addAddressBtn = document.getElementById('add-address-btn');
    const addressFormContainer = document.getElementById('address-form-container');
    let userAddresses = [];

    async function renderAddresses() {
        try {
            const response = await fetch(`${baseURL}api/account/manage_address.php`);
            const result = await response.json();
            userAddresses = result.success ? result.direcciones : [];
        } catch(e) { 
            userAddresses = [];
            console.error("Error al cargar direcciones:", e);
        }

        if (userAddresses.length === 0) {
            addressList.innerHTML = '<p>No tienes direcciones guardadas.</p>';
        } else {
            addressList.innerHTML = userAddresses.map(addr => `
                <div class="address-card">
                    <p><strong>Calle:</strong> ${addr.calle}</p>
                    <p><strong>Colonia:</strong> ${addr.colonia}</p>
                    <p><strong>Ciudad:</strong> ${addr.ciudad}, ${addr.estado}, C.P. ${addr.cp}</p>
                    <div class="address-actions">
                        <button class="btn-edit-address" data-id="${addr.id}">Editar</button>
                        <button class="btn-delete-address" data-id="${addr.id}">Eliminar</button>
                    </div>
                </div>
            `).join('');
        }
        addAddressActionListeners();
    }

    function addAddressActionListeners() {
        document.querySelectorAll('.btn-edit-address').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                const address = userAddresses.find(a => a.id === id);
                if (address) {
                    // Llenamos el formulario (que es controlado por address-manager.js)
                    document.getElementById('address_id').value = address.id;
                    document.getElementById('calle').value = address.calle;
                    document.getElementById('colonia').value = address.colonia;
                    document.getElementById('ciudad').value = address.ciudad;
                    document.getElementById('estado').value = address.estado;
                    document.getElementById('cp').value = address.cp;
                    document.getElementById('referencias').value = address.referencias || '';
                    // Mostramos el formulario y ocultamos el botón de "Añadir"
                    if(addressFormContainer) addressFormContainer.style.display = 'block';
                    if(addAddressBtn) addAddressBtn.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.btn-delete-address').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                Swal.fire({
                    title: '¿Eliminar dirección?',
                    text: "Esta acción no se puede revertir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--azul-vw)',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`${baseURL}api/account/manage_address.php`, {
                                method: 'DELETE',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ address_id: id })
                            });
                            const result = await response.json();
                            if(result.success) {
                                App.showToast('Dirección eliminada', 'success');
                                renderAddresses();
                            } else { throw new Error(result.error); }
                        } catch(error) { App.showToast(error.message, 'error'); }
                    }
                });
            });
        });
    }
    
    // --- INICIALIZACIÓN DE LA PÁGINA ---
    setupTabs();
    renderAddresses();
});