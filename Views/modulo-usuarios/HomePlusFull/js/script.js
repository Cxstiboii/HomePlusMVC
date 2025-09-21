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
