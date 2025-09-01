<?php
session_start(); // 👈 siempre al inicio, antes de cualquier echo o header

require_once '../Model/database.php'; // archivo donde conectas a MySQL

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Acceso no permitido";
    exit;
}

// === Captura de datos del formulario ===
$nombre     = $_POST["nombre"] ?? null;
$apellido   = $_POST["apellido"] ?? null;
$fecha      = $_POST["fecha"] ?? null;
$tipoDoc    = $_POST["tipoDocumento"] ?? null;
$documento  = $_POST["documento"] ?? null;
$telefono   = $_POST["telefono"] ?? null;
$correo     = $_POST["correo"] ?? null;
$direccion  = $_POST["direccion"] ?? null;
$contrasena = $_POST["contrasena"] ?? null;
$confirmar  = $_POST["confirmarContrasena"] ?? null;

// === Validación básica ===
if (!$nombre || !$apellido || !$fecha || !$tipoDoc || !$documento || !$telefono || !$correo || !$direccion || !$contrasena) {
    $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}

if ($contrasena !== $confirmar) {
    $_SESSION['error'] = "❌ Las contraseñas no coinciden.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}

// === Seguridad: encriptar contraseña ===
$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

// === Validar duplicados (correo o documento) ===
$check = $conn->prepare("SELECT id_Usuario FROM usuario WHERE Email = ? OR Numero_Documento = ?");
$check->bind_param("ss", $correo, $documento);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['error'] = "⚠️ Ya existe un usuario con ese correo o documento.";
    $check->close();
    $conn->close();
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}
$check->close();

// === Subida de foto de perfil ===
$fotoPerfil = null;
if (isset($_FILES["fotoPerfil"]) && $_FILES["fotoPerfil"]["error"] === UPLOAD_ERR_OK) {
    $carpeta = "../uploads/perfiles/";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    $nombreArchivo = uniqid() . "_" . basename($_FILES["fotoPerfil"]["name"]);
    $rutaDestino = $carpeta . $nombreArchivo;

    if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $rutaDestino)) {
        $fotoPerfil = $rutaDestino;
    }
}

// === Insertar usuario en la base de datos ===
$sql = "INSERT INTO usuario 
        (Nombres, Apellidos, Fecha_Nacimiento, Tipo_Documento, Numero_Documento, Telefono, Email, Direccion, Contrasena, Foto_Perfil, Tipo_Usuario) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$tipoUsuario = "cliente"; // por defecto cliente, puedes cambiar según tu lógica

$stmt->bind_param("sssssssssss", 
    $nombre, 
    $apellido, 
    $fecha, 
    $tipoDoc, 
    $documento, 
    $telefono, 
    $correo, 
    $direccion, 
    $contrasenaHash,
    $fotoPerfil,
    $tipoUsuario
);

if ($stmt->execute()) {
    $_SESSION['success'] = "✅ Cliente registrado correctamente. Ahora inicie sesión.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit();
} else {
    $_SESSION['error'] = "❌ Error al registrar: " . $stmt->error;
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}

$stmt->close();
$conn->close();
