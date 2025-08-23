  // Navegación entre módulos
  document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item:not(.nav-logout)');
    const modules = document.querySelectorAll('.module');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clase activa de todos los elementos
            navItems.forEach(nav => nav.classList.remove('active'));
            modules.forEach(module => module.classList.remove('active'));
            
            // Agregar clase activa al elemento clickeado
            this.classList.add('active');
            
            // Mostrar el módulo correspondiente
            const moduleId = this.getAttribute('data-module');
            document.getElementById(moduleId).classList.add('active');
        });
    });
});

// Funciones de interacción
function verDetalles(cliente) {
    // Cambiar a módulo de detalles
    document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
    document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));
    
    document.querySelector('[data-module="detalles"]').classList.add('active');
    document.getElementById('detalles').classList.add('active');
    
    // Actualizar información del cliente
    const clienteInfo = {
        'maria-gonzalez': {
            nombre: 'María González',
            servicio: 'Plomería - Reparación de tubería'
        },
        'carlos-perez': {
            nombre: 'Carlos Pérez',
            servicio: 'Electricidad - Instalación de tomas'
        },
        'ana-lopez': {
            nombre: 'Ana López',
            servicio: 'Carpintería - Reparación de puerta'
        }
    };
    
    if (clienteInfo[cliente]) {
        document.getElementById('cliente-nombre').textContent = clienteInfo[cliente].nombre;
        document.getElementById('servicio-tipo').textContent = clienteInfo[cliente].servicio;
    }
    
    mostrarNotificacion('Detalles del trabajo cargados');
}

function confirmarAgendamiento() {
    const fecha = document.getElementById('fecha-servicio').value;
    const hora = document.getElementById('hora-servicio').value;
    
    if (!fecha || !hora) {
        alert('Por favor, selecciona fecha y hora del servicio');
        return;
    }
    
    mostrarNotificacion('¡Agendamiento confirmado exitosamente!');
    
    // Cambiar a módulo de agendamiento
    setTimeout(() => {
        document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
        document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));
        
        document.querySelector('[data-module="agendamiento"]').classList.add('active');
        document.getElementById('agendamiento').classList.add('active');
    }, 1500);
}

function reprogramar() {
    mostrarNotificacion('Función de reprogramación disponible próximamente');
}

function confirmarServicio() {
    mostrarNotificacion('Servicio confirmado exitosamente');
}

function verRuta() {
    mostrarNotificacion('Abriendo navegación GPS...');
}

function mostrarNotificacion(mensaje) {
    const notification = document.getElementById('notification');
    const messageElement = document.getElementById('notificationMessage');
    
    messageElement.textContent = mensaje;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Funciones para el botón de salir
function confirmarSalida(event) {
    event.preventDefault();
    document.getElementById('modalSalir').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalSalir').style.display = 'none';
}

function salirSistema() {
    mostrarNotificacion('Cerrando sesión...');
    setTimeout(() => {
        // Aquí rediriges a la página de login o inicio
        window.location.href = '/modulo-usuarios/HomePlusFull/index.html'; 
    }, 2000);
}

// Establecer fecha mínima como hoy
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha-servicio');
    const today = new Date().toISOString().split('T')[0];
    fechaInput.setAttribute('min', today);
});

// Cerrar modal al hacer clic fuera de él
document.getElementById('modalSalir').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});