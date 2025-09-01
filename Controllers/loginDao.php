<?php
session_start();
require_once '../Model/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}

$correo     = $_POST["email"] ?? null;
$contrasena = $_POST["contrasena"] ?? null;

// Validar campos
if (!$correo || !$contrasena) {
    $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}

// Buscar usuario en la base de datos
$sql = "SELECT id_Usuario, Nombres, Apellidos, Contrasena, Tipo_Usuario 
        FROM usuario WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // Verificar contraseña
    if (password_verify($contrasena, $usuario["Contrasena"])) {
        // Guardar sesión
        $_SESSION["id_Usuario"]   = $usuario["id_Usuario"];
        $_SESSION["nombre"]       = $usuario["Nombres"];
        $_SESSION["apellido"]     = $usuario["Apellidos"];
        $_SESSION["tipo_usuario"] = $usuario["Tipo_Usuario"];

        // Redirigir según tipo de usuario
        if ($usuario["Tipo_Usuario"] === "cliente") {
            header("Location: /Views/modulo-confirmacion-agendamiento/cliente.html");
        } else {
            header("Location: /Views/modulo-confirmacion-agendamiento/profesional.html");
        }
        exit;
    } else {
        $_SESSION['error'] = "❌ Contraseña incorrecta.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "❌ No se encontró un usuario con ese correo.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}

$stmt->close();
$conn->close();
