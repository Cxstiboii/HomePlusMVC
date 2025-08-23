let tipoSeleccionado = ""; // Variable global para saber qué tipo eligió

function seleccionarTipo(tipo) {
  tipoSeleccionado = tipo;
  const camposProfesional = document.getElementById('extra-profesional');

  if (tipo === 'profesional') {
    camposProfesional.style.display = 'flex';
  } else {
    camposProfesional.style.display = 'none';
  }
}

function redirigirAlPerfil(event) {
  event.preventDefault();

  if (tipoSeleccionado === 'profesional') {
    window.location.href = "/modulo-usuarios/HomePlusRegistro/editar-pefil-profesional.html";
  } else if (tipoSeleccionado === 'cliente') {
    window.location.href = "/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.html";
  } else {
    alert("Por favor, selecciona si eres cliente o profesional.");
  }
}
