// ============================
// Modal de negociación CORREGIDO
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
          <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)">
          <input type="number" name="material_cantidad[]" placeholder="Cantidad">
          <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)">
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
        
        // Mostrar loading
        submitBtn.textContent = 'Enviando...';
        submitBtn.disabled = true;
        
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
          console.log('Respuesta del servidor:', data);
          
          if (data.success) {
            alert('✅ ' + data.mensaje);
            modal.style.display = 'none';
            
            // Actualizar el estado en la interfaz si es necesario
            const estadoSpan = document.getElementById("detalle-estado");
            if (estadoSpan) {
              estadoSpan.textContent = "Negociado";
              estadoSpan.classList.remove("status-pendiente");
              estadoSpan.classList.add("status-negociado");
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
  // Funciones para abrir modal de negociación
  // ============================
  
  // Función para abrir el modal con el ID correcto
  function abrirModalNegociacion(idSolicitud) {
    const modal = document.getElementById('negotiationModal');
    const inputIdSolicitud = document.getElementById('idSolicitudNegociacion');
    
    if (inputIdSolicitud) {
      inputIdSolicitud.value = idSolicitud;
      console.log('ID Solicitud asignado para negociación:', idSolicitud);
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
  // Función irDetalles actualizada para incluir botón negociar
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
  
    // Obtener el ID de solicitud
    const idSolicitud = element.dataset.idSolicitud || element.dataset.solicitudId || '';
    console.log('ID Solicitud obtenido:', idSolicitud);
  
    // Asignar ID al botón de agendar
    const agendarBtn = document.getElementById("agendarBtn");
    if (agendarBtn) {
        agendarBtn.dataset.idSolicitud = idSolicitud;
        
        const idSolicitudInput = document.getElementById('idSolicitudInput');
        if (idSolicitudInput) {
            idSolicitudInput.value = idSolicitud;
        }
    }
  
    // IMPORTANTE: Asignar ID al botón de negociar
    const negociarBtn = document.querySelector('.status-badge-negociar-agendar');
    if (negociarBtn) {
        negociarBtn.onclick = function(event) {
            event.preventDefault();
            abrirModalNegociacion(idSolicitud);
        };
    }
  
    // Scroll suave
    if (detallesModule) {
        detallesModule.scrollIntoView({ behavior: 'smooth' });
    }
  }