<?php
// Controllers/PasswordUpdateController.php
session_start();
require_once '../Model/database.php';

// Instanciar la conexión (MySQLi)
$database = new Database();
$db = $database->conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validaciones básicas
    if (empty($token) || empty($password) || empty($confirm)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/restablecerContraseña.php?token=$token");
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/reset_password.php?token=$token");
        exit();
    }

    // Buscar usuario con ese token
    $stmt = $db->prepare("SELECT id, reset_expira FROM usuario WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && strtotime($usuario['reset_expira']) > time()) {
        // Actualizar contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("UPDATE usuario 
                                SET contrasena=?, reset_token=NULL, reset_expira=NULL 
                                WHERE id=?");
        $stmt->bind_param("si", $hash, $usuario['id']);
        $stmt->execute();

        $_SESSION['success'] = "Tu contraseña ha sido actualizada. Ahora puedes iniciar sesión.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/login.php");
        exit();
    } else {
        $_SESSION['error'] = "El enlace de recuperación no es válido o ha expirado.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
        exit();
    }
}
