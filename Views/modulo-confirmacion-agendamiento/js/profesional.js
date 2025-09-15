// ============================
// Navegación entre módulos
// ============================
document.addEventListener('DOMContentLoaded', function() {
  const navItems = document.querySelectorAll('.nav-item:not(.nav-logout)');
  const modules = document.querySelectorAll('.module');
  
  navItems.forEach(item => {
      item.addEventListener('click', function(e) {
          e.preventDefault();
          
          navItems.forEach(nav => nav.classList.remove('active'));
          modules.forEach(module => module.classList.remove('active'));
          
          this.classList.add('active');
          const moduleId = this.getAttribute('data-module');
          document.getElementById(moduleId).classList.add('active');
      });
  });
});

// ============================
// Funciones de interacción
// ============================
function confirmarAgendamiento() {
  const fecha = document.getElementById('fecha-servicio').value;
  const hora = document.getElementById('hora-servicio').value;
  
  if (!fecha || !hora) {
      alert('Por favor, selecciona fecha y hora del servicio');
      return;
  }
  
  mostrarNotificacion('¡Agendamiento confirmado exitosamente!');
  
  setTimeout(() => {
      irAgendamiento();
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

// ============================
// Modal de salida
// ============================
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
      window.location.href = "/Views/modulo-usuarios/HomePlusFull/index.php";
  }, 2000);
}

// ============================
// Redirección a módulo Detalles con datos
// ============================
function irDetalles(btn) {
  // Quitar clases activas de todos
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));

  // Activar módulo de detalles
  document.querySelector('[data-module="detalles"]').classList.add('active');
  document.getElementById('detalles').classList.add('active');

  // ============================
  // Rellenar datos en la sección Detalles
  // ============================
  document.getElementById("cliente-nombre").textContent = btn.dataset.cliente;
  document.getElementById("servicio-tipo").textContent = btn.dataset.servicio;
  
  // Estado
  const estadoEl = document.querySelector("#detalles .status-badge");
  estadoEl.textContent = btn.dataset.estado;
  
  // Urgencia
  const urgenciaEl = document.querySelector("#detalles .status-badge.status-media, #detalles .status-badge.status-aceptado");
  if (urgenciaEl) urgenciaEl.textContent = btn.dataset.urgencia;

  // Puedes guardar también fecha/hora/dirección si los quieres mostrar en otra parte del módulo
  console.log("Dirección:", btn.dataset.direccion);
  console.log("Fecha:", btn.dataset.fecha);
  console.log("Hora:", btn.dataset.hora);
}

// ============================
// Cerrar modal de salida al hacer clic fuera
// ============================
document.getElementById('modalSalir').addEventListener('click', function(e) {
  if (e.target === this) {
      cerrarModal();
  }
});
