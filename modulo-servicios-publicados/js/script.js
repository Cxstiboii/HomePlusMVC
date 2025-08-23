// Función para mostrar/ocultar menú móvil
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

menuBtn.addEventListener('click', function() {
  mobileMenu.classList.toggle('visible');
});

// Cerrar menú móvil al hacer clic fuera
document.addEventListener('click', function(event) {
  if (!menuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
    mobileMenu.classList.remove('visible');
  }
});

// Función para mostrar detalle del servicio
function mostrarDetalle(btn) {
  const card = btn.closest('.servicio-card');
  document.getElementById('detalle-tipo').textContent = card.querySelector('h3').textContent;
  document.getElementById('detalle-direccion').textContent = card.querySelectorAll('p')[0].textContent.replace('Dirección: ', '');
  document.getElementById('detalle-fecha').textContent = card.querySelectorAll('p')[1].textContent.replace('Fecha: ', '');
  document.getElementById('detalle-hora').textContent = card.querySelectorAll('p')[2].textContent.replace('Hora: ', '');
  document.getElementById('detalle-descripcion').textContent = card.querySelectorAll('p')[3].textContent.replace('Descripción: ', '');

  const contenedorImagenes = document.getElementById('detalle-imagenes');
  contenedorImagenes.innerHTML = "";
  const imagenes = btn.dataset.imagenes ? btn.dataset.imagenes.split(',') : [];
  imagenes.forEach(src => {
      const img = document.createElement('img');
      img.src = src.trim();
      img.className = "w-full h-48 object-cover rounded shadow";
      contenedorImagenes.appendChild(img);
  });

  document.getElementById('detalleModal').classList.add('visible');
}

// Función para cerrar detalle
function cerrarDetalle() {
  document.getElementById('detalleModal').classList.remove('visible');
}

// Cerrar modal al hacer clic fuera
document.getElementById('detalleModal').addEventListener('click', function(event) {
  if (event.target === this) {
    cerrarDetalle();
  }
});

// Función para redirigir a negociación
function redirigirANegociacion() {
  window.location.href = "/modulo-solicitudes-negociacion/index.html";
}

// Función para redirigir a confirmación de agendamiento
function redirigirAConfirmacion() {
  window.location.href = "/modulo-confirmacion-agendamiento/profesional.html";
}

// Función para cerrar sesión
function cerrarSesion() {
  window.location.href = "/modulo-usuarios/HomePlusFull/index.html";
}

// Asignar evento a todos los botones de salir
document.querySelectorAll('.btn-salir').forEach(btn => {
  btn.addEventListener('click', cerrarSesion);
});