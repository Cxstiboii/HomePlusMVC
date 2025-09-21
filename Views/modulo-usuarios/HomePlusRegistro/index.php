<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro - Home+</title>
  <link rel="stylesheet" href="/Views/modulo-usuarios/HomePlusRegistro/css/style.css" />
  <link rel="icon" href="/Views/assets/img/1. Usuario/homelogo.jpg">
</head>
<body>

  <style> 
    
  </style>

  <div class="container">
    <h1>Crear cuenta en Home+</h1>

    <!-- Selección tipo de usuario -->
    <div class="user-type">
      <button type="button" onclick="mostrarFormulario('cliente')">Soy Cliente</button>
      <button type="button" onclick="mostrarFormulario('profesional')">Soy Profesional</button>
    </div>

    <!-- Formulario Cliente -->
    <form id="form-cliente" class="formulario" style="display:none;" onsubmit="redirigirAlPerfil(event, 'cliente')">
      <input type="text" placeholder="Nombres" required />
      <input type="text" placeholder="Apellidos" required />
      <input type="email" placeholder="Correo electrónico" required />
      <input type="password" placeholder="Contraseña" required />
      <button type="submit">Registrarse</button>
    </form>

    <!-- Formulario Profesional -->
    <form id="form-profesional" class="formulario" style="display:none;" onsubmit="redirigirAlPerfil(event, 'profesional')">
      <input type="text" placeholder="Nombres" required />
      <input type="text" placeholder="Apellidos" required />

      <select required onchange="mostrarOtroCampo(this)">
        <option value="">-- Selecciona una especialidad --</option>
        <option value="plomería">Plomería</option>
        <option value="electricidad">Electricidad</option>
        <option value="carpintería">Carpintería</option>
        <option value="pintura">Pintura</option>
        <option value="jardinería">Jardinería</option>
        <option value="otro">Otro...</option>
      </select>

      <!-- Se muestra solo si elige "otro" -->
      <div id="campo-otro" class="extra-campos">
        <input type="text" placeholder="Escribe tu especialidad" />
      </div>

      <input type="email" placeholder="Correo electrónico" required />
      <input type="password" placeholder="Contraseña" required />
      <button type="submit">Registrarse</button>
    </form>

    <p class="login-link">¿Ya tienes cuenta? <a href="/Views/modulo-usuarios/HomePlusFull/index.php">Inicia sesión</a></p>
  </div>

  <script src="/Views/modulo-usuarios/HomePlusRegistro/js/script.js"></script>
</body>
</html>

