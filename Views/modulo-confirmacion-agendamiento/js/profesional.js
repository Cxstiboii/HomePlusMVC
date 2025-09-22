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
// Sistema de Filtros Responsive
// ============================
document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.querySelector(".filtro-menu-btn");
  const opciones = document.querySelector(".filtros-opciones");
  const botones = document.querySelectorAll(".filtro-btn");
  const cards = document.querySelectorAll("#trabajos .card[data-estado]"); // Selector correcto para tus cards

  // Toggle menú en móvil
  if (menuBtn && opciones) {
      menuBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          opciones.classList.toggle("show");
          menuBtn.classList.toggle("active");
      });
  }

  // Cerrar menú al hacer clic fuera (solo móvil)
  document.addEventListener("click", (e) => {
      if (window.innerWidth <= 768 && menuBtn && opciones) {
          if (!menuBtn.contains(e.target) && !opciones.contains(e.target)) {
              opciones.classList.remove("show");
              menuBtn.classList.remove("active");
          }
      }
  });

  // Lógica de filtrado
  botones.forEach(btn => {
      btn.addEventListener("click", (e) => {
          e.stopPropagation();
          
          // Quitar active a todos y ponerlo al actual
          botones.forEach(b => b.classList.remove("active"));
          btn.classList.add("active");

          // Actualizar texto del botón móvil
          if (menuBtn) {
              const selectedText = btn.textContent;
              menuBtn.innerHTML = `${selectedText}`;
          }

          // Filtrado de tarjetas
          const estado = btn.dataset.estado;
          cards.forEach(card => {
              const cardEstado = card.dataset.estado;
              if (estado === "todos" || cardEstado === estado) {
                  card.style.display = "block";
              } else {
                  card.style.display = "none";
              }
          });

          // En móvil, cerrar menú después de elegir
          if (window.innerWidth <= 768 && opciones && menuBtn) {
              setTimeout(() => {
                  opciones.classList.remove("show");
                  menuBtn.classList.remove("active");
              }, 200);
          }
      });
  });

  // Detectar cambio de tamaño de pantalla
  window.addEventListener('resize', () => {
      if (window.innerWidth > 768 && opciones && menuBtn) {
          opciones.classList.remove("show");
          menuBtn.classList.remove("active");
          // Restaurar texto original del botón
          menuBtn.innerHTML = 'Filtrar';
      }
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
  
  if (notification && messageElement) {
      messageElement.textContent = mensaje;
      notification.classList.add('show');
      
      setTimeout(() => {
          notification.classList.remove('show');
      }, 3000);
  }
}

// ============================
// Modal de salida
// ============================
function confirmarSalida(event) {
  event.preventDefault();
  const modal = document.getElementById('modalSalir');
  if (modal) {
      modal.style.display = 'flex';
  }
}

function cerrarModal() {
  const modal = document.getElementById('modalSalir');
  if (modal) {
      modal.style.display = 'none';
  }
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
  const detallesNav = document.querySelector('[data-module="detalles"]');
  const detallesModule = document.getElementById('detalles');
  
  if (detallesNav) detallesNav.classList.add('active');
  if (detallesModule) detallesModule.classList.add('active');

  // ============================
  // Rellenar datos en la sección Detalles
  // ============================
  const clienteNombre = document.getElementById("cliente-nombre");
  const servicioTipo = document.getElementById("servicio-tipo");
  const estadoEl = document.querySelector("#detalles .status-badge");
  const detallesFecha = document.getElementById("detalle-fecha");
  const detallesHora = document.getElementById("detalle-hora");
  
  if (clienteNombre) clienteNombre.textContent = btn.dataset.cliente || 'No disponible';
  if (servicioTipo) servicioTipo.textContent = btn.dataset.servicio || 'No disponible';
  if (estadoEl) estadoEl.textContent = btn.dataset.estado || 'No disponible';
  if (detallesFecha) detallesFecha.textContent = btn.dataset.fecha || 'No disponible';
  if (detallesHora) detallesHora.textContent = btn.dataset.hora || 'No disponible';
  
  // Log para debugging
  console.log("Datos cargados:", {
      cliente: btn.dataset.cliente,
      servicio: btn.dataset.servicio,
      estado: btn.dataset.estado,
      direccion: btn.dataset.direccion,
      fecha: btn.dataset.fecha,
      hora: btn.dataset.hora
  });
}

// ============================
// Cerrar modal de salida al hacer clic fuera
// ============================
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('modalSalir');
  if (modal) {
      modal.addEventListener('click', function(e) {
          if (e.target === this) {
              cerrarModal();
          }
      });
  }
});

// ============================
// Slideshow de imágenes
// ============================
let slideIndex = 1;

// Inicializar slideshow cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  const slides = document.getElementsByClassName("mySlides");
  if (slides.length > 0) {
      showSlides(slideIndex);
  }
});

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  
  if (slides.length === 0) return; // No hay slides
  
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  
  if (slides[slideIndex-1]) {
      slides[slideIndex-1].style.display = "block";
  }
  
  if (dots[slideIndex-1]) {
      dots[slideIndex-1].className += " active";
  }
}

// ============================
// Funciones de utilidad adicionales
// ============================

// Función para ir al módulo de agendamiento
function irAgendamiento() {
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));
  
  const agendamientoNav = document.querySelector('[data-module="agendamiento"]');
  const agendamientoModule = document.getElementById('agendamiento');
  
  if (agendamientoNav) agendamientoNav.classList.add('active');
  if (agendamientoModule) agendamientoModule.classList.add('active');
}

// Función para manejar errores de elementos no encontrados
function handleElementNotFound(elementName) {
  console.warn(`Elemento no encontrado: ${elementName}`);
}

// Función para validar si un elemento existe antes de manipularlo
function safeElementOperation(selector, operation) {
  const element = document.querySelector(selector);
  if (element) {
      operation(element);
  } else {
      handleElementNotFound(selector);
  }
}

// ============================
// Event Listeners adicionales
// ============================
document.addEventListener('DOMContentLoaded', function() {
  // Validar que todos los elementos críticos existen
  const criticalElements = [
      '.filtros-trabajos',
      '#trabajos',
      '.nav-item',
      '.module'
  ];
  
  criticalElements.forEach(selector => {
      const element = document.querySelector(selector);
      if (!element) {
          console.warn(`Elemento crítico no encontrado: ${selector}`);
      }
  });
});