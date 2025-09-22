<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nueva Contraseña | Home+</title>
    <link rel="stylesheet" href="/Views/modulo-usuarios/HomePlusFull/css/estilos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="/Views/assets/img/1. Usuario/homelogo.jpg">
</head>
<body>
    <div class="login-wrapper">
        <?php
            $token = $_GET['token'] ?? '';
        ?>
        <form class="login-box" method="POST" action="/Controllers/restablecerContraseñaDao.php">
            <h2>Nueva <span>Contraseña</span></h2>

            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Nueva contraseña" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            </div>

            <button type="submit">Actualizar</button>

            <p class="extra">¿Ya tienes cuenta? <a href="/Views/modulo-usuarios/HomePlusFull/login.php">Inicia sesión</a></p>
        </form>
    </div>
</body>
</html>
