// ============================
// Modal de negociación - VERSIÓN FINAL
// ============================
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById("negotiationModal");
  var span = document.getElementsByClassName("close-btn")[0];

  // Cerrar modal con X
  if (span) {
    span.onclick = function() {
      modal.style.display = "none";
    }
  }

  // Cerrar modal haciendo click fuera
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
        <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)" required>
        <input type="number" name="material_cantidad[]" placeholder="Cantidad" min="1" required>
        <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)" required>
        <button type="button" class="remove-material-btn">❌</button>
      `;

      materialesList.appendChild(newItem);

      // Agregar funcionalidad de eliminar al nuevo item
      newItem.querySelector(".remove-material-btn").addEventListener("click", function(){
        newItem.remove();
      });
    });
  }

  // Manejar envío del formulario de negociación
  const negociarForm = document.getElementById('negociarForm');
  if (negociarForm) {
    negociarForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = document.querySelector('.negociar-btn');
      const originalText = submitBtn.textContent;
      
      // ✅ AGREGAR id_profesional AL FORMDATA
      const idProfesional = document.body.getAttribute('data-id-profesional') || '<?php echo $_SESSION["id_Usuario"] ?? ""; ?>';
      if (idProfesional) {
          formData.append('id_profesional', idProfesional);
      }
      
      console.log('Datos enviados:', Object.fromEntries(formData));
      
      // Mostrar loading
      submitBtn.textContent = 'Enviando...';
      submitBtn.disabled = true;
      
      fetch('../../Controllers/NegociarServicioDao.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        console.log('Respuesta del servidor:', data);
        
        if (data.success) {
          alert('✅ ' + data.mensaje);
          modal.style.display = 'none';
          
          // ⚠️ ELIMINADO: No cambiar el estado visualmente
          // El estado debe permanecer como "Pendiente" en la interfaz
          
          // Recargar la página después de un tiempo
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          // ⚠️ MOSTRAR SOLO EL MENSAJE DEL SERVIDOR SIN "Error:"
          alert('❌ ' + (data.error || 'Error desconocido'));
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
// Funciones para abrir modal de negociación
// ============================

// Función para abrir el modal con el ID correcto
function abrirModalNegociacion(idSolicitud) {
  const modal = document.getElementById('negotiationModal');
  const inputIdSolicitud = document.getElementById('idSolicitudNegociacion');
  
  if (inputIdSolicitud && idSolicitud) {
    inputIdSolicitud.value = idSolicitud;
    console.log('ID Solicitud asignado para negociación:', idSolicitud);
  } else {
    console.error('No se pudo obtener el ID de solicitud');
    return;
  }
  
  if (modal) {
    modal.style.display = 'block';
  }
}

// Función para cerrar el modal
function cerrarNegociacionModal() {
  const modal = document.getElementById('negotiationModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// ============================
// Función cargarDetallesServicio ACTUALIZADA (reemplaza irDetalles)
// ============================
function cargarDetallesServicio(element) {
  // Navegación
  document.querySelectorAll('.nav-item:not(.nav-logout)').forEach(nav => nav.classList.remove('active'));
  document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));

  const detallesModule = document.getElementById('detalles');
  if (detallesModule) {
      detallesModule.classList.add('active');
      detallesModule.style.display = 'block';
  }

  // Obtener datos del elemento
  const idSolicitud = element.getAttribute('data-id-solicitud') || element.getAttribute('data-solicitud-id');
  const servicio = element.getAttribute('data-servicio');
  const descripcion = element.getAttribute('data-descripcion');
  const cliente = element.getAttribute('data-cliente');
  const direccion = element.getAttribute('data-direccion');
  const fecha = element.getAttribute('data-fecha');
  const hora = element.getAttribute('data-hora');
  const estado = element.getAttribute('data-estado');
  const urgencia = element.getAttribute('data-urgencia');
  const precio = element.getAttribute('data-precio');
  
  console.log('Datos del servicio:', { idSolicitud, servicio, estado });

  // Rellenar datos
  document.getElementById("servicio-tipo").textContent = servicio || "No disponible";
  document.getElementById("detalle-descripcion").textContent = descripcion || "No disponible";
  document.getElementById("detalle-cliente").textContent = cliente || "No disponible";
  document.getElementById("detalle-direccion").textContent = direccion || "No disponible";
  document.getElementById("detalle-fecha").textContent = fecha || "No disponible";
  document.getElementById("detalle-hora").textContent = hora || "No disponible";
  document.getElementById("detalle-estado").textContent = estado || "No disponible";
  document.getElementById("detalle-urgencia").textContent = urgencia || "No disponible";
  document.getElementById("detalle-precio").textContent = precio ? `$${parseInt(precio).toLocaleString()}` : "$0";

  // Actualizar estado visual
  const estadoSpan = document.getElementById("detalle-estado");
  if (estadoSpan) {
      estadoSpan.className = "status-badge status-" + estado.toLowerCase();
  }

  // ✅ ASIGNAR CORRECTAMENTE EL BOTÓN DE NEGOCIAR
  const negociarBtn = document.getElementById("negociarBtn");
  if (negociarBtn && idSolicitud) {
      console.log('Asignando negociarBtn con ID:', idSolicitud);
      negociarBtn.onclick = function(event) {
          event.preventDefault();
          event.stopPropagation();
          abrirModalNegociacion(idSolicitud);
      };
      
      // También guardar el ID en un data attribute por si acaso
      negociarBtn.setAttribute('data-id-solicitud', idSolicitud);
  } else {
      console.error('No se encontró el botón de negociar o el ID de solicitud');
  }

  // Asignar ID al botón de agendar
  const agendarBtn = document.getElementById("agendarBtn");
  if (agendarBtn && idSolicitud) {
      agendarBtn.dataset.idSolicitud = idSolicitud;
      
      const idSolicitudInput = document.getElementById('idSolicitudInput');
      if (idSolicitudInput) {
          idSolicitudInput.value = idSolicitud;
      }
  }

  // Manejar imágenes del slideshow si las hay
  const imagenes = element.getAttribute('data-imagenes');
  if (imagenes) {
      try {
          const imagenesArray = JSON.parse(imagenes);
          cargarImagenesSlideshow(imagenesArray);
      } catch (e) {
          console.error('Error al parsear imágenes:', e);
      }
  }

  // Scroll suave
  if (detallesModule) {
      detallesModule.scrollIntoView({ behavior: 'smooth' });
  }
}

// Función para cargar imágenes en el slideshow - CORREGIDA
function cargarImagenesSlideshow(imagenesArray) {
  const slidesContainer = document.getElementById('slides-container');
  const dotsContainer = document.getElementById('dots-container');
  
  if (slidesContainer && dotsContainer) {
      slidesContainer.innerHTML = '';
      dotsContainer.innerHTML = '';
      
      console.log('Imágenes recibidas para slideshow:', imagenesArray);
      
      if (imagenesArray && imagenesArray.length > 0) {
          imagenesArray.forEach((imagen, index) => {
              // Verificar que la imagen tenga una ruta válida
              if (!imagen || imagen === 'null' || imagen === '') {
                  console.warn('Imagen vacía o inválida en índice:', index);
                  return; // Saltar esta imagen
              }
              
              // Crear slide
              const slide = document.createElement('div');
              slide.className = 'mySlides fade';
              
              // Usar onerror para manejar imágenes rotas
              slide.innerHTML = `
                  <img src="${imagen}" 
                       style="width:100%; height:300px; object-fit:cover;" 
                       onerror="this.src='/Views/assets/imagenes-comunes/servicios/default.jpg'"
                       alt="Imagen del servicio">
              `;
              
              slidesContainer.appendChild(slide);
              
              // Crear dot
              const dot = document.createElement('span');
              dot.className = 'dot';
              dot.onclick = function() { currentSlide(index + 1); };
              dotsContainer.appendChild(dot);
          });
          
          // Reiniciar el slideshow
          slideIndex = 1;
          showSlides(slideIndex);
          
      } else {
          // Imagen por defecto si no hay imágenes
          console.log('No hay imágenes, cargando imagen por defecto');
          slidesContainer.innerHTML = `
              <div class="mySlides fade">
                  <img src="/Views/assets/imagenes-comunes/servicios/default.jpg" 
                        style="width:100%; height:300px; object-fit:cover;"
                        alt="Imagen por defecto">
              </div>
          `;
          // Agregar un dot para la imagen por defecto
          const dot = document.createElement('span');
          dot.className = 'dot';
          dotsContainer.appendChild(dot);
          
          // Reiniciar slideshow
          slideIndex = 1;
          showSlides(slideIndex);
      }
  } else {
      console.error('No se encontraron los contenedores del slideshow');
  }
}