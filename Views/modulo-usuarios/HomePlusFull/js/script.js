document.getElementById("loginForm").addEventListener("submit", function(e) {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const error = document.getElementById("error");

  if (!email || !password) {
    e.preventDefault(); // ✅ solo lo usas aquí para frenar si faltan campos
    error.textContent = "Completa todos los campos.";
    return;
  }
  // 🚀 Dejas que el form se envíe a loginDao.php
});


// En tu script.js
function validarPassword() {
  const password = document.querySelector('input[name="password"]');
  const confirm = document.querySelector('input[name="confirm_password"]');
  
  if (password.value !== confirm.value) {
      alert('Las contraseñas no coinciden');
      return false;
  }
  
  if (password.value.length < 6) {
      alert('La contraseña debe tener al menos 6 caracteres');
      return false;
  }
  
  return true;
}