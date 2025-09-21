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


let slideIndex = 1;
showSlides(slideIndex);

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
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}


document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.querySelector(".filtro-menu-btn");
  const opciones = document.querySelector(".filtros-opciones");
  const botones = document.querySelectorAll(".filtro-btn");
  const cards = document.querySelectorAll("#trabajos .card[data-estado]");

  // Toggle menú en móvil
  if (menuBtn) {
    menuBtn.addEventListener("click", () => {
      opciones.classList.toggle("show");
      menuBtn.classList.toggle("active");
    });
  }

  // Lógica de filtrado
  botones.forEach(btn => {
    btn.addEventListener("click", () => {
      // Quitar active a todos y ponerlo al actual
      botones.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      // Filtrado de tarjetas
      const estado = btn.dataset.estado;
      cards.forEach(card => {
        if (estado === "todos" || card.dataset.estado === estado) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });

      // En móvil, cerrar menú después de elegir
      if (window.innerWidth <= 768) {
        opciones.classList.remove("show");
        menuBtn.classList.remove("active");
      }
    });
  });
});
