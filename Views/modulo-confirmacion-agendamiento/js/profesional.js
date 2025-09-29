// ============================
// Navegación entre módulos - CORREGIDA
// ============================
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item:not(.nav-logout)');
    const modules = document.querySelectorAll('.module');
    
    // Función para cambiar de módulo
    function cambiarModulo(moduleId) {
        // Ocultar todos los módulos
        modules.forEach(module => {
            module.classList.remove('active');
            module.style.display = 'none';
        });
        
        // Mostrar el módulo seleccionado
        const targetModule = document.getElementById(moduleId);
        if (targetModule) {
            targetModule.classList.add('active');
            targetModule.style.display = 'block';
        }
        
        // Actualizar navegación activa
        navItems.forEach(nav => nav.classList.remove('active'));
        const activeNav = document.querySelector(`[data-module="${moduleId}"]`);
        if (activeNav) {
            activeNav.classList.add('active');
        }
    }
    
    // Event listeners para navegación principal
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const moduleId = this.getAttribute('data-module');
            cambiarModulo(moduleId);
        });
    });
    
    // Mostrar módulo inicial (Mis Trabajos)
    cambiarModulo('trabajos');
});

// ============================
// Función para cargar detalles del servicio - CORREGIDA
// ============================
function cargarDetallesServicio(element) {
    // Obtener datos del atributo data-
    const idSolicitud = element.getAttribute('data-id-solicitud');
    const servicio = element.getAttribute('data-servicio');
    const descripcion = element.getAttribute('data-descripcion');
    const cliente = element.getAttribute('data-cliente');
    const direccion = element.getAttribute('data-direccion');
    const fecha = element.getAttribute('data-fecha');
    const hora = element.getAttribute('data-hora');
    const estado = element.getAttribute('data-estado');
    const urgencia = element.getAttribute('data-urgencia');
    const precio = element.getAttribute('data-precio');
    
    // Obtener array de imágenes
    const imagenesJson = element.getAttribute('data-imagenes');
    const imagenesArray = imagenesJson ? JSON.parse(imagenesJson) : [element.getAttribute('data-img-principal')];

    // Actualizar la información en el módulo de detalles
    document.getElementById('servicio-tipo').textContent = servicio;
    document.getElementById('detalle-descripcion').textContent = descripcion;
    document.getElementById('detalle-cliente').textContent = cliente;
    document.getElementById('detalle-direccion').textContent = direccion;
    document.getElementById('detalle-fecha').textContent = fecha;
    document.getElementById('detalle-hora').textContent = hora;
    document.getElementById('detalle-estado').textContent = estado;
    document.getElementById('detalle-urgencia').textContent = urgencia;
    document.getElementById('detalle-precio').textContent = '$' + parseInt(precio).toLocaleString();

    // IMPORTANTE: Guardar el ID de solicitud en el botón agendar y negociar
    const agendarBtn = document.getElementById("agendarBtn");
    const negociarBtn = document.querySelector(".status-badge-negociar-agendar.status-badge-beige");
    
    if (agendarBtn) {
        agendarBtn.dataset.idSolicitud = idSolicitud;
        
        console.log('ID Solicitud asignado al agendar:', idSolicitud);
        
        // También actualizar el formulario del modal
        const idSolicitudInput = document.getElementById('idSolicitudInput');
        if (idSolicitudInput) {
            idSolicitudInput.value = idSolicitud;
        }
    }

    // Configurar botón de negociar
    if (negociarBtn && idSolicitud) {
        negociarBtn.onclick = function() {
            abrirNegociacionModal(idSolicitud);
        };
    }

    // Cargar las imágenes en el slideshow
    cargarSlideshow(imagenesArray);

    // Cambiar al módulo de detalles
    cambiarModulo('detalles');
    
    // Scroll suave
    const detallesModule = document.getElementById('detalles');
    if (detallesModule) {
        detallesModule.scrollIntoView({ behavior: 'smooth' });
    }
}

// ============================
// Función auxiliar para cambiar módulos (debe estar disponible globalmente)
// ============================
function cambiarModulo(moduleId) {
    const navItems = document.querySelectorAll('.nav-item:not(.nav-logout)');
    const modules = document.querySelectorAll('.module');
    
    // Ocultar todos los módulos
    modules.forEach(module => {
        module.classList.remove('active');
        module.style.display = 'none';
    });
    
    // Mostrar el módulo seleccionado
    const targetModule = document.getElementById(moduleId);
    if (targetModule) {
        targetModule.classList.add('active');
        targetModule.style.display = 'block';
    }
    
    // Actualizar navegación activa
    navItems.forEach(nav => nav.classList.remove('active'));
    const activeNav = document.querySelector(`[data-module="${moduleId}"]`);
    if (activeNav) {
        activeNav.classList.add('active');
    }
}

  
  // ============================
  // Sistema de Filtros Responsive - MEJORADO
  // ============================
  document.addEventListener("DOMContentLoaded", () => {
    const menuBtn = document.querySelector(".filtro-menu-btn");
    const opciones = document.querySelector(".filtros-opciones");
    const botones = document.querySelectorAll(".filtro-btn");
  
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
  
    // Lógica de filtrado MEJORADA - Recarga la página con el filtro
    botones.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            
            const estado = btn.dataset.estado;
            
            // Obtener parámetros actuales de la URL
            const urlParams = new URLSearchParams(window.location.search);
            
            // Actualizar el parámetro de estado
            if (estado === "todos") {
                urlParams.delete('estado');
            } else {
                urlParams.set('estado', estado);
            }
            
            // Construir nueva URL
            const nuevaURL = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            
            // Recargar la página con el nuevo filtro
            window.location.href = nuevaURL;
  
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
            // Actualizar texto del botón según el estado actual
            const urlParams = new URLSearchParams(window.location.search);
            const estadoActual = urlParams.get('estado') || 'todos';
            const textos = {
                'todos': 'Filtrar',
                'Aceptado': 'Aceptados',
                'Pendiente': 'Pendientes',
                'Cancelado': 'Cancelados', 
                'Negociado': 'Negociados',
                'Agendado': 'Agendados'
            };
            menuBtn.innerHTML = textos[estadoActual] || 'Filtrar';
        }
    });
  });
  
  // ============================
  // Funciones de interacción
  // ============================
  function mostrarNotificacion(mensaje) {
    // Crear notificación si no existe
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 10000;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;
        document.body.appendChild(notification);
    }
    
    notification.textContent = mensaje;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
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
  // Variables globales para slideshow
  // ============================
  let slideIndex = 1;
  let currentImages = [];
  
  // ============================
  // Sistema de Slideshow Dinámico
  // ============================
  function cargarSlideshow(imagenes) {
      currentImages = imagenes;
      const slidesContainer = document.getElementById('slides-container');
      const dotsContainer = document.getElementById('dots-container');
      
      // Limpiar contenedores
      slidesContainer.innerHTML = '';
      dotsContainer.innerHTML = '';
      
      // Si no hay imágenes, mostrar una por defecto
      if (imagenes.length === 0) {
          imagenes = ['/Views/assets/imagenes-comunes/servicios/electricista.jpg'];
          currentImages = imagenes;
      }
      
      // Crear slides
      imagenes.forEach((imagen, index) => {
          const slideDiv = document.createElement('div');
          slideDiv.className = 'mySlides fade';
          slideDiv.innerHTML = `
              <div class="numbertext">${index + 1} / ${imagenes.length}</div>
              <img src="${imagen}" style="width:100%; border-radius:20px; height: 400px; object-fit: cover;" 
                   alt="Imagen ${index + 1} del servicio">
          `;
          slidesContainer.appendChild(slideDiv);
          
          // Crear dots
          const dot = document.createElement('span');
          dot.className = 'dot';
          dot.onclick = function() { currentSlide(index + 1); };
          dotsContainer.appendChild(dot);
      });
      
      // Inicializar slideshow
      slideIndex = 1;
      showSlides(slideIndex);
  }
  
  // Funciones del slideshow
  function plusSlides(n) {
      showSlides(slideIndex += n);
  }
  
  function currentSlide(n) {
      showSlides(slideIndex = n);
  }
  
  function showSlides(n) {
      let i;
      const slides = document.getElementsByClassName("mySlides");
      const dots = document.getElementsByClassName("dot");
      
      if (slides.length === 0) return;
      
      if (n > slides.length) { slideIndex = 1; }
      if (n < 1) { slideIndex = slides.length; }
      
      for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
      }
      
      for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
      }
      
      if (slides[slideIndex - 1]) {
          slides[slideIndex - 1].style.display = "block";
      }
      if (dots[slideIndex - 1]) {
          dots[slideIndex - 1].className += " active";
      }
  }
  
  // Navegación con teclado
  document.addEventListener('keydown', function(event) {
      const detallesModule = document.getElementById('detalles');
      if (detallesModule && detallesModule.style.display === 'block') {
          if (event.key === 'ArrowLeft') {
              plusSlides(-1);
          } else if (event.key === 'ArrowRight') {
              plusSlides(1);
          }
      }
  });
  
  // ============================
  // Función para cargar detalles del servicio con slideshow
  // ============================
  function cargarDetallesServicio(element) {
      // Navegación
      document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
      document.querySelectorAll('.module').forEach(module => {
          module.classList.remove('active');
          // Ocultar todos los módulos excepto detalles
          if (module.id !== 'detalles') {
              module.style.display = 'none';
          }
      });
  
      const detallesModule = document.getElementById('detalles');
      if (detallesModule) {
          detallesModule.style.display = 'block';
          detallesModule.classList.add('active');
          
          // Activar el nav-item de detalles
          document.querySelector('[data-module="detalles"]').classList.add('active');
      }
  
      // Obtener datos del atributo data-
      const idSolicitud = element.getAttribute('data-id-solicitud');
      const servicio = element.getAttribute('data-servicio');
      const descripcion = element.getAttribute('data-descripcion');
      const cliente = element.getAttribute('data-cliente');
      const direccion = element.getAttribute('data-direccion');
      const fecha = element.getAttribute('data-fecha');
      const hora = element.getAttribute('data-hora');
      const estado = element.getAttribute('data-estado');
      const urgencia = element.getAttribute('data-urgencia');
      const precio = element.getAttribute('data-precio');
      
      // Obtener array de imágenes
      const imagenesJson = element.getAttribute('data-imagenes');
      const imagenesArray = imagenesJson ? JSON.parse(imagenesJson) : [element.getAttribute('data-img-principal')];
  
      // Actualizar la información en el modal de detalles
      document.getElementById('servicio-tipo').textContent = servicio;
      document.getElementById('detalle-descripcion').textContent = descripcion;
      document.getElementById('detalle-cliente').textContent = cliente;
      document.getElementById('detalle-direccion').textContent = direccion;
      document.getElementById('detalle-fecha').textContent = fecha;
      document.getElementById('detalle-hora').textContent = hora;
      document.getElementById('detalle-estado').textContent = estado;
      document.getElementById('detalle-urgencia').textContent = urgencia;
      document.getElementById('detalle-precio').textContent = '$' + parseInt(precio).toLocaleString();
  
      // IMPORTANTE: Guardar el ID de solicitud en el botón agendar y negociar
      const agendarBtn = document.getElementById("agendarBtn");
      const negociarBtn = document.querySelector(".status-badge-negociar-agendar.status-badge-beige");
      
      if (agendarBtn) {
          agendarBtn.dataset.idSolicitud = idSolicitud;
          
          console.log('ID Solicitud asignado al agendar:', idSolicitud);
          
          // También actualizar el formulario del modal
          const idSolicitudInput = document.getElementById('idSolicitudInput');
          if (idSolicitudInput) {
              idSolicitudInput.value = idSolicitud;
          }
      }
  
      // Convertir el enlace de negociar en botón funcional
      if (negociarBtn && idSolicitud) {
          negociarBtn.onclick = function() {
              abrirNegociacionModal(idSolicitud);
          };
      }
  
      // Cargar las imágenes en el slideshow
      cargarSlideshow(imagenesArray);
  
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
  // SISTEMA DE NEGOCIACIÓN
  // ============================
  
  // Abre el modal de negociación
  function abrirNegociacionModal(idSolicitud) {
      if (!idSolicitud) {
          alert("❌ Error: No se pudo obtener la información del servicio.");
          return;
      }
      
      // Asignar el ID al campo oculto
      document.getElementById('idSolicitudNegociacion').value = idSolicitud;
      
      // Mostrar el modal
      document.getElementById('negotiationModal').style.display = 'block';
      
      console.log("Modal abierto para solicitud:", idSolicitud);
  }
  
  // Cierra el modal de negociación
  function cerrarNegociacionModal() {
      document.getElementById('negotiationModal').style.display = 'none';
      // Limpiar formulario
      document.getElementById('negociarForm').reset();
      // Restaurar solo un campo de material
      const materialesList = document.getElementById('materiales-list');
      materialesList.innerHTML = `
          <div class="material-item">
              <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)" required>
              <input type="number" name="material_cantidad[]" placeholder="Cantidad" min="1" required>
              <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)" required>
              <button type="button" class="remove-material-btn" onclick="this.parentElement.remove()">❌</button>
          </div>
      `;
      
      // Ocultar mensajes
      const mensaje = document.getElementById('negociacionMensaje');
      if (mensaje) mensaje.style.display = 'none';
  }
  
  // Agrega nuevos campos de material
  function agregarMaterial() {
      const materialesList = document.getElementById('materiales-list');
      const nuevoMaterial = document.createElement('div');
      nuevoMaterial.className = 'material-item';
      nuevoMaterial.innerHTML = `
          <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)" required>
          <input type="number" name="material_cantidad[]" placeholder="Cantidad" min="1" required>
          <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)" required>
          <button type="button" class="remove-material-btn" onclick="this.parentElement.remove()">❌</button>
      `;
      materialesList.appendChild(nuevoMaterial);
  }
  
  // Mostrar mensajes en el modal de negociación
  function mostrarMensajeNegociacion(mensaje, tipo) {
      const mensajeDiv = document.getElementById('negociacionMensaje');
      if (mensajeDiv) {
          mensajeDiv.textContent = mensaje;
          mensajeDiv.style.display = 'block';
          mensajeDiv.style.color = tipo === 'success' ? 'green' : 'red';
          mensajeDiv.style.padding = '10px';
          mensajeDiv.style.borderRadius = '5px';
          mensajeDiv.style.backgroundColor = tipo === 'success' ? '#f0fff0' : '#fff0f0';
          mensajeDiv.style.border = `1px solid ${tipo === 'success' ? 'green' : 'red'}`;
      }
  }
  
  // ============================
  // MODAL DE AGENDAMIENTO
  // ============================
  
  // Función para abrir el modal con información del servicio
  function abrirModalConfirmacion(servicioData) {
      // Llenar los campos ocultos del formulario
      document.getElementById('idSolicitudInput').value = servicioData.id_solicitud || '';
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
  
  // ============================
  // INICIALIZACIÓN AL CARGAR LA PÁGINA
  // ============================
  document.addEventListener('DOMContentLoaded', function() {
      // Inicializar modal de negociación
      const negociarForm = document.getElementById('negociarForm');
      if (negociarForm) {
          negociarForm.addEventListener('submit', function(e) {
              e.preventDefault();
              
              // Validación básica
              const precio = document.getElementById('precio_estimado').value;
              if (!precio || precio <= 0) {
                  mostrarMensajeNegociacion('❌ Por favor ingresa un precio válido', 'error');
                  return;
              }
              
              // Mostrar loading
              const submitBtn = this.querySelector('button[type="submit"]');
              const originalText = submitBtn.textContent;
              submitBtn.textContent = 'Enviando...';
              submitBtn.disabled = true;
              
              // Preparar datos del formulario
              const formData = new FormData(this);
              
              // Enviar via AJAX
              fetch('../../Controllers/NegociarServicioDao.php', {
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
                  console.log('Respuesta negociación:', data);
                  
                  if (data.success) {
                      mostrarMensajeNegociacion('✅ ' + data.mensaje, 'success');
                      
                      // Cerrar modal después de 2 segundos
                      setTimeout(() => {
                          cerrarNegociacionModal();
                          
                          // Actualizar estado en la interfaz
                          const estadoSpan = document.getElementById("detalle-estado");
                          if (estadoSpan) {
                              estadoSpan.textContent = "Negociado";
                              estadoSpan.className = "status-badge status-negociado";
                          }
                          
                          // Mostrar notificación de éxito
                          mostrarNotificacion('✅ Oferta enviada correctamente. El servicio ahora aparece en "Mis Trabajos" como Negociado.');
                          
                          // Recargar la página para ver cambios en Mis Trabajos
                          setTimeout(() => {
                              location.reload();
                          }, 2000);
                      }, 2000);
                  } else {
                      mostrarMensajeNegociacion('❌ Error: ' + data.error, 'error');
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  mostrarMensajeNegociacion('❌ Error de conexión: ' + error.message, 'error');
              })
              .finally(() => {
                  // Restaurar botón
                  submitBtn.textContent = originalText;
                  submitBtn.disabled = false;
              });
          });
      }
      
      // Cerrar modal de negociación al hacer clic fuera
      const modalNegociacion = document.getElementById('negotiationModal');
      if (modalNegociacion) {
          modalNegociacion.addEventListener('click', function(e) {
              if (e.target === this) {
                  cerrarNegociacionModal();
              }
          });
      }
  
      // Inicializar modal de agendamiento
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
                        mostrarNotificacion('✅ ' + data.mensaje);
                        confirmModal.style.display = 'none';

                        // Actualizar el estado en la interfaz
                        const estadoSpan = document.getElementById("detalle-estado");
                        if (estadoSpan) {
                            estadoSpan.textContent = "Agendado";
                            estadoSpan.className = "status-badge status-agendado";
                        }

                      // Mostrar mensaje de éxito
                        const mensaje = document.getElementById("agendarMensaje");
                        if (mensaje) {
                            mensaje.style.display = "block";
                            mensaje.textContent = data.mensaje;
                            mensaje.style.color = "green";
                        }

                      // Recargar la página después de un tiempo
                      setTimeout(() => {
                            location.reload();
                      }, 2000);
                  } else {
                        mostrarNotificacion('❌ Error: ' + (data.error || 'Error desconocido'));
                  }
              })
              .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('❌ Error de conexión: ' + error.message);
              })
              .finally(() => {
                  // Restaurar botón
                  submitBtn.textContent = originalText;
                  submitBtn.disabled = false;
              });
          });
      }
  });




  //NAV HAMBURGUESA
  document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const mainContent = document.getElementById('main-content');
    
    // Alternar menú al hacer clic en el botón hamburguesa
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
    
    // Cerrar menú al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            hamburger.classList.remove('active');
        });
    }
    
    // Cerrar menú al hacer clic en un enlace (opcional para móviles)
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                if (hamburger) hamburger.classList.remove('active');
            }
        });
    });

    // Navegación entre módulos
    const navLinks = document.querySelectorAll('.nav-item[data-module]');
    const modules = document.querySelectorAll('.module');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clase active de todos los enlaces y módulos
            navLinks.forEach(nav => nav.classList.remove('active'));
            modules.forEach(module => module.classList.remove('active'));
            
            // Agregar active al enlace clickeado
            this.classList.add('active');
            
            // Mostrar el módulo correspondiente
            const moduleId = this.getAttribute('data-module');
            const targetModule = document.getElementById(moduleId);
            if (targetModule) {
                targetModule.classList.add('active');
            }
        });
    });
});