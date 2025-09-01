<?php
session_start(); // 👈 siempre al inicio

require_once '../Model/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

// 1. Datos comunes del usuario
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
$servicio    = $_POST["servicio"] ?? null;
$experiencia = $_POST["experiencia"] ?? null;

// === Validaciones básicas ===
if (!$nombres || !$apellidos || !$fecha || !$tipoDoc || !$documento || !$telefono || !$correo || !$direccion || !$contrasena || !$servicio || !$experiencia) {
    $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

if ($contrasena !== $confirmar) {
    $_SESSION['error'] = "❌ Las contraseñas no coinciden.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
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
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}
$check->close();

// 2. Insertar en la tabla usuario
$sqlUsuario = "INSERT INTO usuario 
    (Nombres, Apellidos, Fecha_Nacimiento, Tipo_Documento, Numero_Documento, Telefono, Email, Direccion, Contrasena, Tipo_Usuario) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'profesional')";

$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("sssssssss", 
    $nombres, $apellidos, $fecha, $tipoDoc, $documento, $telefono, $correo, $direccion, $contrasenaHash
);

if ($stmtUsuario->execute()) {
    $idUsuario = $stmtUsuario->insert_id; // 🔑 obtenemos el id del usuario

    // 3. Insertar en la tabla profesional
    $historial    = "";   // valor inicial
    $calificacion = 0.0;  // valor inicial

    $sqlProfesional = "INSERT INTO profesional 
        (id_Profesional, experiencia, historial, especialidad, calificaciones) 
        VALUES (?, ?, ?, ?, ?)";

    $stmtProf = $conn->prepare($sqlProfesional);
    $stmtProf->bind_param("isssd", 
        $idUsuario, $experiencia, $historial, $servicio, $calificacion
    );

    if ($stmtProf->execute()) {
        $_SESSION['success'] = "✅ Profesional registrado correctamente. Ahora inicie sesión.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
        exit();
    } else {
        $_SESSION['error'] = "❌ Error al registrar en profesional: " . $stmtProf->error;
        header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
        exit;
    }
    $stmtProf->close();
} else {
    $_SESSION['error'] = "❌ Error al registrar en usuario: " . $stmtUsuario->error;
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-profesional.php");
    exit;
}

$stmtUsuario->close();
$conn->close();
