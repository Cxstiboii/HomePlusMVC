<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recoger datos del formulario
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    
    // Si es "otro", usar la especialidad personalizada
    if ($especialidad === 'otro' && isset($_POST['especialidad_personalizada'])) {
        $especialidad = $_POST['especialidad_personalizada'];
    }
    
    // Validaciones básicas
    if (empty($nombres) || empty($apellidos) || empty($email) || empty($contrasena)) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
        header("Location: /Views/modulo-usuarios/HomePlusRegistro/index.php");
        exit;
    }
    
    // Guardar datos en sesión para el formulario completo
    $_SESSION['registro_pendiente'] = [
        'tipo_usuario' => $tipo_usuario,
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'email' => $email,
        'contrasena' => $contrasena,
        'especialidad' => $especialidad
    ];
    
    // Redirigir al formulario completo correspondiente
    if ($tipo_usuario === 'cliente') {
        header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    } else {
        header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    }
    exit;
} else {
    $_SESSION['error'] = "Acceso no permitido";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/index.php");
    exit;
}
?>