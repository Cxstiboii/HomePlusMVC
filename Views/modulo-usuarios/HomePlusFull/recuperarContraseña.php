<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Recuperar Contraseña | Home+</title>
    <link rel="stylesheet" href="/Views/modulo-usuarios/HomePlusFull/css/estilos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="/Views/assets/img/1. Usuario/homelogo.jpg">
</head>
<body>
    <div class="login-wrapper">

        <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div style="background:#f8d7da;color:#721c24;padding:10px;margin-bottom:15px;border:1px solid #f5c6cb;border-radius:5px;">'
                    . $_SESSION['error'] .
                '</div>';
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo '<div style="background:#d4edda;color:#155724;padding:10px;margin-bottom:15px;border:1px solid #c3e6cb;border-radius:5px;">'
                    . $_SESSION['success'] .
                '</div>';
                unset($_SESSION['success']);
            }
        ?>
        <form class="login-box" method="POST" action="/Controllers/recuperarContraseñaDao.php">
            <h2>Recuperar <span>Contraseña</span></h2>

            <p style="margin-bottom:20px; color:#555;">Ingresa tu correo electrónico para enviarte un enlace de recuperación.</p>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>

            <button type="submit">Enviar enlace</button>

            <p class="extra">¿Ya la recordaste? <a href="/Views/modulo-usuarios/HomePlusFull/index.php">Inicia sesión</a></p>
        </form>
    </div>
</body>
</html>
