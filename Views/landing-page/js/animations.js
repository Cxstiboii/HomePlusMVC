document.addEventListener("DOMContentLoaded", function () {
    const clienteInput = document.getElementById("toggle-on");
    const profesionalInput = document.getElementById("toggle-off");
    const clienteText = document.getElementById("clienteText");
    const profesionalText = document.getElementById("profesionalText");
    const toggleWrapper = document.getElementById("toggleWrapper");

    function actualizarTexto() {
    if (clienteInput.checked) {
        clienteText.classList.add("show");
        profesionalText.classList.remove("show");
        toggleWrapper.classList.remove("profesional-active");
    } else if (profesionalInput.checked) {
        clienteText.classList.remove("show");
        profesionalText.classList.add("show");
        toggleWrapper.classList.add("profesional-active");
    }
    }

    // Inicializar al cargar
    actualizarTexto();

    // Escuchar cambios
    clienteInput.addEventListener("change", actualizarTexto);
    profesionalInput.addEventListener("change", actualizarTexto);
});

// Añade esto al final de tu script existente
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // Cierra el menú móvil si está abierto
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
            }
        }
    });
});