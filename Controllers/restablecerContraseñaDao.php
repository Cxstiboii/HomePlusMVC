<?php
// Controllers/restablecerContraseñaDao.php
session_start();
require_once '../Model/database.php';
require_once '../vendor/autoload.php'; // 👈 Agregar PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Instanciar la conexión
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

    if (strlen($password) < 6) {
        $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/restablecerContraseña.php?token=$token");
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/restablecerContraseña.php?token=$token");
        exit();
    }

    try {
        // Buscar usuario con ese token
        $stmt = $db->prepare("SELECT id_Usuario, Email, Nombres, reset_expira FROM usuario WHERE reset_token=?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        if ($usuario) {
            // Verificar si el token no ha expirado
            if (strtotime($usuario['reset_expira']) > time()) {
                // Actualizar contraseña
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $db->prepare("UPDATE usuario 
                                    SET Contrasena=?, reset_token=NULL, reset_expira=NULL 
                                    WHERE id_Usuario=?");
                $stmt->bind_param("si", $hash, $usuario['id_Usuario']);
                
                if ($stmt->execute()) {
                    // 📧 ENVIAR NOTIFICACIÓN DE CAMBIO DE CONTRASEÑA
                    enviarNotificacionCambioPassword($usuario['Email'], $usuario['Nombres']);
                    
                    $_SESSION['success'] = "✅ Tu contraseña ha sido actualizada. Ahora puedes iniciar sesión.";
                    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "❌ Error al actualizar la contraseña.";
                }
            } else {
                $_SESSION['error'] = "❌ El enlace de recuperación ha expirado.";
            }
        } else {
            $_SESSION['error'] = "❌ El enlace de recuperación no es válido.";
        }

    } catch (Exception $e) {
        error_log("Error al restablecer contraseña: " . $e->getMessage());
        $_SESSION['error'] = "Error en el servidor. Inténtalo más tarde.";
    }

    header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
    exit();
}

// 🔧 FUNCIÓN PARA ENVIAR NOTIFICACIÓN DE CAMBIO DE CONTRASEÑA
function enviarNotificacionCambioPassword($email, $nombre) {
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
        $mail->Subject = 'Contraseña actualizada - Home+';
        
        $mail->Body = "
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
                    padding: 25px; 
                    text-align: center; 
                    border-radius: 10px 10px 0 0; 
                    margin: -20px -20px 20px -20px;
                }
                .success-box {
                    background: var(--light-beige);
                    border: 1px solid var(--beige);
                    border-radius: 8px;
                    padding: 15px;
                    margin: 15px 0;
                }
                .info-box {
                    background: var(--light-beige);
                    border: 1px solid var(--gray);
                    padding: 15px;
                    border-radius: 8px;
                    margin: 15px 0;
                }
                .footer { 
                    margin-top: 20px; 
                    padding-top: 20px; 
                    border-top: 1px solid var(--gray); 
                    color: var(--black); 
                    text-align: center;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Contraseña Actualizada</h1>
                </div>
                
                <p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
                
                <div class='success-box'>
                    <h3>✅ Tu contraseña ha sido actualizada exitosamente</h3>
                    <p><strong>Fecha y hora:</strong> " . date('d/m/Y H:i:s') . "</p>
                </div>
                
                <div class='info-box'>
                    <h4>📋 Detalles del cambio:</h4>
                    <p><strong>• Hora:</strong> " . date('H:i:s') . "</p>
                    <p><strong>• Fecha:</strong> " . date('d/m/Y') . "</p>
                    <p><strong>• Estado:</strong> Contraseña actualizada correctamente</p>
                </div>
                
                <p>Si no realizaste este cambio, por favor:</p>
                <ul>
                    <li>Contacta inmediatamente a nuestro soporte</li>
                    <li>Revisa la seguridad de tu cuenta</li>
                    <li>Considera habilitar la verificación en dos pasos</li>
                </ul>
                
                <div class='footer'>
                    <p>Este es un mensaje automático de seguridad.</p>
                    <p><strong>El equipo de Home+</strong></p>
                    <p style='color: var(--gray); font-size: 12px;'>
                        ¿Necesitas ayuda? Responde a este correo o contacta a soporte.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->AltBody = "Notificación de seguridad - Home+\n\n" .
                        "Hola $nombre,\n\n" .
                        "Tu contraseña ha sido actualizada exitosamente.\n\n" .
                        "Fecha: " . date('d/m/Y') . "\n" .
                        "Hora: " . date('H:i:s') . "\n\n" .
                        "Si no realizaste este cambio, contacta inmediatamente a nuestro soporte.\n\n" .
                        "Este es un mensaje automático de seguridad.\n" .
                        "El equipo de Home+";

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error enviando notificación de cambio de contraseña: " . $e->getMessage());
        return false;
    }
}
?>