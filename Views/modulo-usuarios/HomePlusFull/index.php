<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inicio de Sesion | Home+</title>
    <link rel="stylesheet" href="/Views/modulo-usuarios/HomePlusFull/css/estilos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="/Views/assets/img/1. Usuario/homelogo.jpg">
    <style>
    body {
        background-image: url('/Views/assets/img/1. Usuario/int1.jpeg');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '
                <div style="background-color: #f8d7da; color: #721c24; 
                            padding: 15px; text-align: center; 
                            font-weight: bold; border: 1px solid #f5c6cb; 
                            margin-bottom: 20px;">
                    ' . $_SESSION['error'] . '
                </div>';
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo '
                <div style="background-color: #d4edda; color: #155724; 
                            padding: 15px; text-align: center; 
                            font-weight: bold; border: 1px solid #c3e6cb; 
                            margin-bottom: 20px;">
                    ' . $_SESSION['success'] . '
                </div>';
                unset($_SESSION['success']);
            }
        ?>
        <form class="login-box" id="loginForm" method="POST" action="/Controllers/loginDao.php">
            <h2>Bienvenido a <span>Home+</span></h2>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Correo electronico" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="contrasena" placeholder="Contrasena" required>
            </div>

            <button type="submit">Iniciar Sesión</button>

            <p id="error" class="error-msg"></p>
            <p class="extra">¿No tienes cuenta? <a href="/Views/modulo-usuarios/HomePlusRegistro/index.php">Regístrate</a></p>
            <p class="extra">¿Olvidaste la contraseña? <a href="/Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php">Recupérala</a></p>

            
        </form>
    </div>

    <script src="/Views/modulo-usuarios/HomePlusFull/js/script.js"></script>
</body>
</html>
