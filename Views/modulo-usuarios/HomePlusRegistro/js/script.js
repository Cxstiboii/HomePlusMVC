function mostrarFormulario(tipo) {
  document.getElementById("form-cliente").style.display = (tipo === "cliente") ? "flex" : "none";
  document.getElementById("form-profesional").style.display = (tipo === "profesional") ? "flex" : "none";
}

function mostrarOtroCampo(select) {
  const campoOtro = document.getElementById("campo-otro");
  campoOtro.style.display = (select.value === "otro") ? "flex" : "none";
}

function redirigirAlPerfil(event, tipo) {
  event.preventDefault();

  if (tipo === "profesional") {
    window.location.href = "/Views/modulo-usuarios/HomePlusRegistro/editar-pefil-profesional.html";
  } else if (tipo === "cliente") {
    window.location.href = "/Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.html";
  }
}

