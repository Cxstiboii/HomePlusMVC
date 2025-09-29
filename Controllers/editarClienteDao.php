<?php
session_start();
require_once '../Model/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$conn = $db->conn;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}

// Captura de datos
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

// Validaciones
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
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}
$check->close();

// Subida de foto de perfil
$fotoPerfil = null;
if (isset($_FILES["fotoPerfil"]) && $_FILES["fotoPerfil"]["error"] === UPLOAD_ERR_OK) {
    $carpeta = "../uploads/perfiles/";
    if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);
    $nombreArchivo = uniqid() . "_" . basename($_FILES["fotoPerfil"]["name"]);
    $rutaDestino = $carpeta . $nombreArchivo;
    if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $rutaDestino)) $fotoPerfil = $rutaDestino;
}

// Insertar en usuario
$sql = "INSERT INTO usuario 
        (Nombres, Apellidos, Fecha_Nacimiento, Tipo_Documento, Numero_Documento, Telefono, Email, Direccion, Contrasena, Foto_Perfil, Tipo_Usuario)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$tipoUsuario = "cliente";
$stmt->bind_param("sssssssssss", $nombre, $apellido, $fecha, $tipoDoc, $documento, $telefono, $correo, $direccion, $contrasenaHash, $fotoPerfil, $tipoUsuario);

if ($stmt->execute()) {
    $idUsuario = $stmt->insert_id;

    // Insertar en tabla cliente
    $sqlCliente = "INSERT INTO cliente (id_cliente, servicios_solicitados, citas_solicitadas, calificaciones) VALUES (?, 0, 0, 0.0)";
    $stmtCliente = $conn->prepare($sqlCliente);
    $stmtCliente->bind_param("i", $idUsuario);
    $stmtCliente->execute();
    $stmtCliente->close();

    // 📧 ENVIAR EMAIL DE BIENVENIDA AL CLIENTE
    enviarEmailBienvenida($correo, $nombre, 'cliente');

    $_SESSION['success'] = "✅ Cliente registrado correctamente. Se ha enviado un email de bienvenida.";
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
} else {
    $_SESSION['error'] = "❌ Error al registrar: " . $stmt->error;
    header("Location: /Views/modulo-usuarios/HomePlusRegistro/editar-perfil-cliente.php");
    exit;
}

$stmt->close();
$conn->close();

// 🔧 FUNCIÓN PARA ENVIAR EMAIL DE BIENVENIDA
function enviarEmailBienvenida($email, $nombre, $tipoUsuario, $especialidad = null) {
    try {
        $mail = new PHPMailer(true);
        
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samuelcastillo1007@gmail.com';
        $mail->Password   = 'sddk vnhv htmj fvxl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Destinatarios
        $mail->setFrom('samuelcastillo1007@gmail.com', 'Home+');
        $mail->addAddress($email, $nombre);
        $mail->addReplyTo('samuelcastillo1007@gmail.com', 'Soporte Home+');

        // Contenido del email
        $mail->isHTML(true);
        
        if ($tipoUsuario === 'profesional') {
            $mail->Subject = '¡Bienvenido a Home+ - Cuenta Profesional!';
            $mail->Body = crearEmailProfesional($nombre, $especialidad);
        } else {
            $mail->Subject = '¡Bienvenido a Home+!';
            $mail->Body = crearEmailCliente($nombre);
        }

        $mail->AltBody = "¡Bienvenido a Home+, $nombre!\n\nTu cuenta $tipoUsuario ha sido creada exitosamente.\n\nGracias por confiar en nosotros.\nEl equipo de Home+";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error enviando email de bienvenida: " . $e->getMessage());
        return false;
    }
}

// 📝 PLANTILLA EMAIL PARA CLIENTES CON TUS COLORES
function crearEmailCliente($nombre) {
    return "
    <html>
    <head>
        <style>
            :root {
                --white: #ffffff;
                --black: #1f1f1f;
                --beige: #ccb58e;
                --gray: #c2c2c2;
                --light-beige: #f5f0e6;
                --dark-beige: #b8a57b;
            }
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6;
                color: var(--black);
                background-color: var(--light-beige);
                margin: 0;
                padding: 20px;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px;
                border: 1px solid var(--beige);
                border-radius: 10px;
                background-color: var(--white);
            }
            .header { 
                background: linear-gradient(135deg, var(--beige), var(--dark-beige)); 
                color: var(--white); 
                padding: 30px; 
                text-align: center; 
                border-radius: 10px 10px 0 0; 
                margin: -20px -20px 20px -20px;
            }
            .features { 
                background: var(--light-beige); 
                padding: 20px; 
                border-radius: 8px; 
                margin: 20px 0;
                border: 1px solid var(--beige);
            }
            .feature-item { 
                margin: 10px 0; 
                padding-left: 20px;
                color: var(--black);
            }
            .footer { 
                margin-top: 20px; 
                padding-top: 20px; 
                border-top: 1px solid var(--gray); 
                color: var(--black); 
                text-align: center;
            }
            .welcome-text {
                font-size: 16px;
                margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Bienvenido a Home+!</h1>
            </div>
            
            <p class='welcome-text'>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
            
            <p>¡Estamos emocionados de tenerte en nuestra comunidad! Tu cuenta ha sido creada exitosamente.</p>
            
            <div class='features'>
                <h3>📱 ¿Qué puedes hacer en Home+?</h3>
                <div class='feature-item'>✅ Agendar servicios de limpieza</div>
                <div class='feature-item'>✅ Gestionar tus citas</div>
                <div class='feature-item'>✅ Ver el historial de servicios</div>
                <div class='feature-item'>✅ Calificar a los profesionales</div>
                <div class='feature-item'>✅ Explorar diferentes servicios para el hogar</div>
            </div>
            
            <p>Si tienes alguna pregunta, no dudes en responder a este correo.</p>
            
            <div class='footer'>
                <p>¡Gracias por confiar en nosotros!</p>
                <p><strong>El equipo de Home+</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

// 📝 PLANTILLA EMAIL PARA PROFESIONALES CON TUS COLORES
function crearEmailProfesional($nombre, $especialidad) {
    return "
    <html>
    <head>
        <style>
            :root {
                --white: #ffffff;
                --black: #1f1f1f;
                --beige: #ccb58e;
                --gray: #c2c2c2;
                --light-beige: #f5f0e6;
                --dark-beige: #b8a57b;
            }
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6;
                color: var(--black);
                background-color: var(--light-beige);
                margin: 0;
                padding: 20px;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px;
                border: 1px solid var(--beige);
                border-radius: 10px;
                background-color: var(--white);
            }
            .header { 
                background: linear-gradient(135deg, var(--beige), var(--dark-beige)); 
                color: var(--white); 
                padding: 30px; 
                text-align: center; 
                border-radius: 10px 10px 0 0; 
                margin: -20px -20px 20px -20px;
            }
            .features { 
                background: var(--light-beige); 
                padding: 20px; 
                border-radius: 8px; 
                margin: 20px 0;
                border: 1px solid var(--beige);
            }
            .feature-item { 
                margin: 10px 0; 
                padding-left: 20px;
                color: var(--black);
            }
            .footer { 
                margin-top: 20px; 
                padding-top: 20px; 
                border-top: 1px solid var(--gray); 
                color: var(--black); 
                text-align: center;
            }
            .welcome-text {
                font-size: 16px;
                margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Bienvenido Profesional!</h1>
            </div>
            
            <p class='welcome-text'>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
            
            <p>¡Estamos emocionados de tenerte en nuestro equipo de profesionales!</p>
            
            <div class='features'>
                <h3>🚀 ¿Qué puedes hacer en Home+?</h3>
                <div class='feature-item'>✅ Gestionar tus servicios de <strong>$especialidad</strong></div>
                <div class='feature-item'>✅ Recibir solicitudes de clientes</div>
                <div class='feature-item'>✅ Administrar tu agenda y citas</div>
                <div class='feature-item'>✅ Crear tu perfil profesional</div>
                <div class='feature-item'>✅ Recibir calificaciones de clientes</div>
            </div>
            
            <p>Próximamente nuestro equipo se pondrá en contacto contigo para validar tu documentación y activar completamente tu cuenta.</p>
            
            <div class='footer'>
                <p>¡Gracias por unirte a nuestra comunidad!</p>
                <p><strong>El equipo de Home+</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>