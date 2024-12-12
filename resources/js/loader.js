// Mostrar el loader
function showLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.classList.add('visible');
    }
}

// Ocultar el loader
function hideLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.classList.remove('visible');
    }
}

// Evento al cargar la página
window.addEventListener('load', () => {
    hideLoader(); // Ocultar el loader al cargar la página
});

// Evento para navegación AJAX o enlaces
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a[href]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            showLoader();
        });
    });
});
