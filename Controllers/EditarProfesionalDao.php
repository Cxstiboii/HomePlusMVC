<?php
session_start();
require_once '../Model/database.php';

$db = new Database(); // instanciamos la clase
$conn = $db->conn;    // obtenemos la conexión mysqli

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

// Datos del formulario
$nombres     = $_POST["nombres"] ?? null;
$apellidos   = $_POST["apellidos"] ?? null;
$fecha       = $_POST["fechaNacimiento"] ?? null;
$tipoDoc     = $_POST["tipoDocumento"] ?? null;
$documento   = $_POST["documento"] ?? null;
$telefono    = $_POST["telefono"] ?? null;
$correo      = $_POST["email"] ?? null;
$direccion   = $_POST["direccion"] ?? null;
$contrasena  = $_POST["contrasena"] ?? null;
$confirmar   = $_POST["confirmarContrasena"] ?? null;
$experiencia = $_POST["experiencia"] ?? null;
$servicio    = $_POST["servicio"] ?? null;

// Validaciones
if (!$nombres || !$apellidos || !$fecha || !$tipoDoc || !$documento || !$telefono || !$correo || !$direccion || !$contrasena || !$experiencia || !$servicio) {
    $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

if ($contrasena !== $confirmar) {
    $_SESSION['error'] = "❌ Las contraseñas no coinciden.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

// Encriptar contraseña
$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

// Validar duplicados
$check = $conn->prepare("SELECT id_Usuario FROM usuario WHERE Email = ? OR Numero_Documento = ?");
$check->bind_param("ss", $correo, $documento);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $_SESSION['error'] = "⚠️ Ya existe un usuario con ese correo o documento.";
    $check->close();
    $conn->close();
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}
$check->close();

// Insertar en usuario
$sqlUsuario = "INSERT INTO usuario 
    (Nombres, Apellidos, Fecha_Nacimiento, Tipo_Documento, Numero_Documento, Telefono, Email, Direccion, Contrasena, Tipo_Usuario)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'profesional')";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("sssssssss", $nombres, $apellidos, $fecha, $tipoDoc, $documento, $telefono, $correo, $direccion, $contrasenaHash);

if ($stmtUsuario->execute()) {
    $idUsuario = $stmtUsuario->insert_id;

    // Insertar en tabla profesional
    $historial = "";
    $calificacion = 0.0;
    $sqlProf = "INSERT INTO profesional (id_Profesional, experiencia, historial, especialidad, calificaciones) VALUES (?, ?, ?, ?, ?)";
    $stmtProf = $conn->prepare($sqlProf);
    $stmtProf->bind_param("isssd", $idUsuario, $experiencia, $historial, $servicio, $calificacion);
    $stmtProf->execute();
    $stmtProf->close();

    $_SESSION['success'] = "✅ Profesional registrado correctamente. Ahora inicie sesión.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
} else {
    $_SESSION['error'] = "❌ Error al registrar en usuario: " . $stmtUsuario->error;
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

$stmtUsuario->close();
$conn->close();
?>
