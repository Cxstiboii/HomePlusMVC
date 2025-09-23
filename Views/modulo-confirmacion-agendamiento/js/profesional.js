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
  const cards = document.querySelectorAll("#trabajos .card[data-estado]");

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
          
          botones.forEach(b => b.classList.remove("active"));
          btn.classList.add("active");

          if (menuBtn) {
              const selectedText = btn.textContent;
              menuBtn.innerHTML = `${selectedText}`;
          }

          const estado = btn.dataset.estado;
          cards.forEach(card => {
              const cardEstado = card.dataset.estado;
              if (estado === "todos" || cardEstado === estado) {
                  card.style.display = "block";
              } else {
                  card.style.display = "none";
              }
          });

          if (window.innerWidth <= 768 && opciones && menuBtn) {
              setTimeout(() => {
                  opciones.classList.remove("show");
                  menuBtn.classList.remove("active");
              }, 200);
          }
      });
  });

  window.addEventListener('resize', () => {
      if (window.innerWidth > 768 && opciones && menuBtn) {
          opciones.classList.remove("show");
          menuBtn.classList.remove("active");
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

// Cerrar modal de salida al hacer clic fuera
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

document.addEventListener('DOMContentLoaded', function() {
  const slides = document.getElementsByClassName("mySlides");
  if (slides.length > 0) {
      showSlides(slideIndex);
  }
});

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  
  if (slides.length === 0) return;
  
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
// Funciones de utilidad
// ============================
function irAgendamiento() {
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));
  
  const agendamientoNav = document.querySelector('[data-module="agendamiento"]');
  const agendamientoModule = document.getElementById('agendamiento');
  
  if (agendamientoNav) agendamientoNav.classList.add('active');
  if (agendamientoModule) agendamientoModule.classList.add('active');
}

function irDetalles(element) {
  // Navegación
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));

  const detallesModule = document.getElementById('detalles');
  if (detallesModule) {
    detallesModule.classList.add('active');
  }

  // Rellenar datos
  document.getElementById("servicio-tipo").textContent = element.dataset.servicio || "No disponible";
  document.getElementById("detalle-descripcion").textContent = element.dataset.descripcion || "No disponible";
  document.getElementById("detalle-cliente").textContent = element.dataset.cliente || "No disponible";
  document.getElementById("detalle-direccion").textContent = element.dataset.direccion || "No disponible";
  document.getElementById("detalle-fecha").textContent = element.dataset.fecha || "No disponible";
  document.getElementById("detalle-hora").textContent = element.dataset.hora || "No disponible";
  document.getElementById("detalle-estado").textContent = element.dataset.estado || "No disponible";
  document.getElementById("detalle-urgencia").textContent = element.dataset.urgencia || "No disponible";
  document.getElementById("detalle-precio").textContent = `$${element.dataset.precio || "0"}`;

  if (element.dataset.img) {
      document.getElementById("detalle-img").src = element.dataset.img;
  }

  // Guardar el ID de solicitud en el botón agendar
  const agendarBtn = document.getElementById("agendarBtn");
  if (agendarBtn && element.dataset.idSolicitud) {
      agendarBtn.dataset.idSolicitud = element.dataset.idSolicitud;
  }

  // Scroll suave
  if (detallesModule) {
    detallesModule.scrollIntoView({ behavior: 'smooth' });
  }
}

// ============================
// Preview de imagen de perfil
// ============================
document.addEventListener('DOMContentLoaded', function() {
    const inputFile = document.getElementById('foto-perfil-input');
    const previewImg = document.getElementById('foto-preview');

    if (inputFile && previewImg) {
        inputFile.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => previewImg.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    }
});

// ============================
// Modal de negociación
// ============================
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById("negotiationModal");
  var btn = document.querySelector(".status-badge-negociar-agendar");
  var span = document.getElementsByClassName("close-btn")[0];

  if (btn) {
    btn.onclick = function(event) {
      event.preventDefault();
      modal.style.display = "block";
    }
  }

  if (span) {
    span.onclick = function() {
      modal.style.display = "none";
    }
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }

  // Agregar materiales
  var addMaterialBtn = document.querySelector(".add-material-btn");
  var materialesList = document.getElementById("materiales-list");

  if (addMaterialBtn && materialesList) {
    addMaterialBtn.addEventListener("click", function(e) {
      e.preventDefault();

      var newItem = document.createElement("div");
      newItem.classList.add("material-item");

      newItem.innerHTML = `
        <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)">
        <input type="number" name="material_cantidad[]" placeholder="Cantidad">
        <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)">
        <button type="button" class="remove-material-btn">❌</button>
      `;

      materialesList.appendChild(newItem);

      newItem.querySelector(".remove-material-btn").addEventListener("click", function(){
        newItem.remove();
      });
    });
  }
});

// ============================
// MODAL DE AGENDAMIENTO MEJORADO
// ============================

// Función para abrir el modal con información del servicio
function abrirModalConfirmacion(servicioData) {
  // Llenar los campos ocultos del formulario
  document.getElementById('idSolicitudInput').value = servicioData.id_solicitud || '';
  document.getElementById('idServicioPublicadoInput').value = servicioData.id_servicio_publicado || '';
  document.getElementById('nombreServicioInput').value = servicioData.nombre_servicio || '';
  document.getElementById('precioServicioInput').value = servicioData.precio_servicio || '';
  document.getElementById('descripcionServicioInput').value = servicioData.descripcion_servicio || '';
  
  // Mostrar información en el modal
  document.getElementById('nombreServicio').textContent = servicioData.nombre_servicio || 'Servicio';
  document.getElementById('descripcionServicio').textContent = servicioData.descripcion_servicio || 'Sin descripción';
  document.getElementById('precioServicio').textContent = '$' + parseFloat(servicioData.precio_servicio || 0).toLocaleString('es-CO');
  document.getElementById('duracionServicio').textContent = servicioData.duracion || 'Por definir';
  
  // Mostrar el modal
  document.getElementById('confirmModal').style.display = 'block';
}

// Inicializar modal de agendamiento
document.addEventListener('DOMContentLoaded', function() {
  const confirmModal = document.getElementById("confirmModal");
  const closeConfirm = document.querySelector(".close-confirm");
  const cancelBtn = document.getElementById("cancelarBtn");
  const agendarBtn = document.getElementById("agendarBtn");

  // Abrir modal cuando se hace clic en "Agendar"
  if (agendarBtn) {
      agendarBtn.addEventListener("click", function(e) {
          e.preventDefault();
          
          // Obtener datos del elemento actual en detalles
          const servicioData = {
              id_solicitud: this.dataset.idSolicitud || '',
              id_servicio_publicado: '', // Este se debe obtener de algún lado
              nombre_servicio: document.getElementById('servicio-tipo').textContent,
              descripcion_servicio: document.getElementById('detalle-descripcion').textContent,
              precio_servicio: document.getElementById('detalle-precio').textContent.replace('$', '').replace(/\./g, ''),
              duracion: 'Por definir'
          };
          
          abrirModalConfirmacion(servicioData);
      });
  }

  // Cerrar modal al hacer click en "x"
  if (closeConfirm) {
      closeConfirm.addEventListener("click", function() {
          confirmModal.style.display = "none";
      });
  }

  // Cerrar modal al hacer click en "Cancelar"
  if (cancelBtn) {
      cancelBtn.addEventListener("click", function() {
          confirmModal.style.display = "none";
      });
  }

  // Cerrar modal al hacer click fuera del contenido
  window.addEventListener("click", function(event) {
      if (event.target === confirmModal) {
          confirmModal.style.display = "none";
      }
  });

  // Manejar el envío del formulario de agendamiento
  const agendarForm = document.getElementById('agendarForm');
  if (agendarForm) {
      agendarForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          // Mostrar loading
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;
          submitBtn.textContent = 'Agendando...';
          submitBtn.disabled = true;
          
          fetch('../../Controllers/AgendarServicioDao.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error('Error en la respuesta del servidor');
              }
              return response.json();
          })
          .then(data => {
              console.log('Respuesta del servidor:', data);
              
              if (data.success) {
                  alert('✅ ' + data.mensaje);
                  confirmModal.style.display = 'none';
                  
                  // Actualizar el estado en la interfaz
                  const estadoSpan = document.getElementById("detalle-estado");
                  if (estadoSpan) {
                      estadoSpan.textContent = "Agendado";
                      estadoSpan.classList.remove("status-pendiente");
                      estadoSpan.classList.add("status-agendado");
                  }
                  
                  // Mostrar mensaje de éxito
                  const mensaje = document.getElementById("agendarMensaje");
                  if (mensaje) {
                      mensaje.style.display = "block";
                      mensaje.textContent = data.mensaje;
                      mensaje.style.color = "green";
                  }
                  
                  // Opcional: recargar la página después de un tiempo
                  setTimeout(() => {
                      location.reload();
                  }, 2000);
              } else {
                  alert('❌ Error: ' + (data.error || 'Error desconocido'));
                  console.error('Error del servidor:', data);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('❌ Error de conexión: ' + error.message);
          })
          .finally(() => {
              // Restaurar botón
              submitBtn.textContent = originalText;
              submitBtn.disabled = false;
          });
      });
  }
});

// ============================
// Función para pasar ID de solicitud al hacer clic en "Ver Detalles"
// ============================
function irDetalles(element) {
  // Navegación
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));

  const detallesModule = document.getElementById('detalles');
  if (detallesModule) {
      detallesModule.classList.add('active');
  }

  // Rellenar datos
  document.getElementById("servicio-tipo").textContent = element.dataset.servicio || "No disponible";
  document.getElementById("detalle-descripcion").textContent = element.dataset.descripcion || "No disponible";
  document.getElementById("detalle-cliente").textContent = element.dataset.cliente || "No disponible";
  document.getElementById("detalle-direccion").textContent = element.dataset.direccion || "No disponible";
  document.getElementById("detalle-fecha").textContent = element.dataset.fecha || "No disponible";
  document.getElementById("detalle-hora").textContent = element.dataset.hora || "No disponible";
  document.getElementById("detalle-estado").textContent = element.dataset.estado || "No disponible";
  document.getElementById("detalle-urgencia").textContent = element.dataset.urgencia || "No disponible";
  document.getElementById("detalle-precio").textContent = `$${element.dataset.precio || "0"}`;

  if (element.dataset.img) {
      document.getElementById("detalle-img").src = element.dataset.img;
  }

  // IMPORTANTE: Guardar el ID de solicitud en el botón agendar
  const agendarBtn = document.getElementById("agendarBtn");
  if (agendarBtn) {
      // Obtener el ID de solicitud del elemento (debe venir del PHP)
      const idSolicitud = element.dataset.idSolicitud || element.dataset.solicitudId || '';
      agendarBtn.dataset.idSolicitud = idSolicitud;
      
      console.log('ID Solicitud asignado:', idSolicitud); // Para debug
      
      // También actualizar el formulario del modal
      const idSolicitudInput = document.getElementById('idSolicitudInput');
      if (idSolicitudInput) {
          idSolicitudInput.value = idSolicitud;
      }
  }

  // Scroll suave
  if (detallesModule) {
      detallesModule.scrollIntoView({ behavior: 'smooth' });
  }
}