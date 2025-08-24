document.getElementById("loginForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const error = document.getElementById("error");

  if (!email || !password) {
    error.textContent = "Completa todos los campos.";
    return;
  }


  const usuarios = [
    { email: "cliente@homeplus.com", password: "1234", rol: "cliente" },
    { email: "pro@homeplus.com", password: "5678", rol: "profesional" }
  ];

  const usuarioValido = usuarios.find(user =>
    user.email === email &&
    user.password === password
  );

  if (usuarioValido) {
    alert(`¡Inicio exitoso! Usted se registró como ${usuarioValido.rol}.`);

   
    if (usuarioValido.rol === "cliente") {
      window.location.href = "/Views/modulo-confirmacion-agendamiento/cliente.html";
    } else if (usuarioValido.rol === "profesional") {
      window.location.href = "/Views/modulo-servicios-publicados/index.html";
    }
  } else {
    error.textContent = "Correo o contraseña incorrectos.";
  }
});

