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
            session_start();
            $token = $_GET['token'] ?? '';
            
            // Mostrar mensajes de error/success
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
        
        <form class="login-box" method="POST" action="/Controllers/restablecerContraseñaDao.php">
            <h2>Nueva <span>Contraseña</span></h2>

            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Nueva contraseña" required minlength="6">
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required minlength="6">
            </div>

            <button type="submit">Actualizar Contraseña</button>

            <p class="extra">¿Ya tienes cuenta? <a href="/Views/modulo-usuarios/HomePlusFull/index.php">Inicia sesión</a></p>
        </form>
    </div>
</body>
</html>