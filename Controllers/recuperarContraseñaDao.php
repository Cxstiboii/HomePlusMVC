<?php
// Controllers/recuperarContraseñaDao.php
session_start();
require_once '../Model/database.php';

// Instanciar la conexión
$database = new Database();
$db = $database->conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Validar formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El correo no es válido.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
        exit();
    }

    try {
        // Buscar usuario
        $stmt = $db->prepare("SELECT id FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Guardar token
            $stmt = $db->prepare("UPDATE usuario SET reset_token=?, reset_expira=? WHERE id=?");
            $stmt->bind_param("ssi", $token, $expira, $usuario['id']);
            $stmt->execute();

            $link = "http://localhost/HomePlusMVC/Views/modulo-usuarios/HomePlusFull/reset_password.php?token=$token";

            $asunto = "Recuperar contraseña - Home+";
            $mensaje = "Hola,\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n\n$link\n\nEste enlace expira en 1 hora.";
            $cabeceras = "From: no-reply@homeplus.com";

            @mail($email, $asunto, $mensaje, $cabeceras);

            $_SESSION['success'] = "✅ Hemos enviado un enlace de recuperación a tu correo.";
        } else {
            $_SESSION['error'] = "❌ No se encuentra el correo deseado.";
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Error en el servidor. Inténtalo más tarde.";
    }

    header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
    exit();
}
