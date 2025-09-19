// js/main.js - Lógica para la interfaz principal y funciones globales (VERSIÓN UNIFICADA Y CORRECTA)

// =================================================================
// 1. DEFINICIÓN DE NUESTRA CAJA DE HERRAMIENTAS GLOBAL (`App`)
// Se define fuera del DOMContentLoaded para que esté disponible inmediatamente.
// =================================================================
const App = {};

/**
 * Muestra una notificación "toast" no intrusiva con la configuración correcta.
 * @param {string} title - El mensaje principal a mostrar.
 * @param {string} icon - El tipo de icono ('success', 'error', 'warning', 'info', 'question').
 */
App.showToast = function(title, icon = 'success') {
    // Verificamos si Swal existe antes de usarlo, por si acaso.
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado. No se puede mostrar el toast.');
        // Como fallback, mostramos una alerta nativa.
        alert(title);
        return;
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        target: document.getElementById('portal-container'), // Usamos el portal para evitar conflictos
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: icon,
        title: title
    });
};


// =================================================================
// 2. LÓGICA QUE DEPENDE DEL DOM
// Se ejecuta solo cuando el HTML está completamente listo.
// =================================================================
document.addEventListener('DOMContentLoaded', () => {
    // --- Lógica para la interactividad del sidebar ---
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const openBtn = document.getElementById('open-sidebar-btn');

    const openSidebar = () => {
        if (sidebar) sidebar.classList.add('visible');
        if (backdrop) backdrop.classList.add('visible');
    };

    const closeSidebar = () => {
        if (sidebar) sidebar.classList.remove('visible');
        if (backdrop) backdrop.classList.remove('visible');
    };

    if (sidebar && backdrop && openBtn) {
        openBtn.addEventListener('click', openSidebar);
        backdrop.addEventListener('click', closeSidebar);
    }
});