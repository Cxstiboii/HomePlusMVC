<?php
// Controllers/recuperarContraseñaDao.php
session_start();
require_once '../Model/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El correo no es válido.";
        header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
        exit();
    }

    try {
        // Buscar usuario
        $stmt = $db->prepare("SELECT id_Usuario, Nombres FROM usuario WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Guardar token
            $stmt = $db->prepare("UPDATE usuario SET reset_token=?, reset_expira=? WHERE id_Usuario=?");
            $stmt->bind_param("ssi", $token, $expira, $usuario['id_Usuario']);
            
            if ($stmt->execute()) {
                // ✅ CORREGIDO: Usar el nombre correcto del archivo
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $link = "$protocol://$host/Views/modulo-usuarios/HomePlusFull/restablecerContraseña.php?token=$token";

                // Configurar PHPMailer
                $mail = new PHPMailer(true);
                
                try {
                    // Configuración del servidor
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'homeplus608@gmail.com';
                    $mail->Password   = 'ferw gfih hqlq ymsc';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    // Destinatarios
                    $mail->setFrom('samuelcastillo1007@gmail.com', 'Home+');
                    $mail->addAddress($email, $usuario['Nombres']);
                    $mail->addReplyTo('samuelcastillo1007@gmail.com', 'Soporte Home+');

                    // Contenido
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperar contraseña - Home+';
                    
                    $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                line-height: 1.6;
                                color: #333;
                            }
                            .container { 
                                max-width: 600px; 
                                margin: 0 auto; 
                                padding: 20px;
                                border: 1px solid #ddd;
                                border-radius: 10px;
                            }
                            .button { 
                                background-color: #007bff; 
                                color: white; 
                                padding: 12px 24px; 
                                text-decoration: none; 
                                border-radius: 5px; 
                                display: inline-block;
                            }
                            .button:hover {
                                background-color: #0056b3;
                            }
                            .link-box { 
                                background: #f8f9fa; 
                                padding: 15px; 
                                border-radius: 5px; 
                                word-break: break-all; 
                                margin: 15px 0;
                                border: 1px solid #e9ecef;
                            }
                            .footer {
                                margin-top: 20px;
                                padding-top: 20px;
                                border-top: 1px solid #eee;
                                color: #666;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Hola " . htmlspecialchars($usuario['Nombres']) . ",</h2>
                            <p>Has solicitado restablecer tu contraseña en Home+.</p>
                            <p>Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='$link' class='button'>Restablecer Contraseña</a>
                            </p>
                            <p>O copia y pega este enlace en tu navegador:</p>
                            <div class='link-box'>
                                <code>$link</code>
                            </div>
                            <p><strong>⚠️ Este enlace expira en 1 hora.</strong></p>
                            <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                            <div class='footer'>
                                <p>Saludos,<br><strong>El equipo de Home+</strong></p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";

                    $mail->AltBody = "Hola " . $usuario['Nombres'] . ",\n\nPara restablecer tu contraseña en Home+, haz clic en el siguiente enlace:\n\n$link\n\nEste enlace expira en 1 hora.\n\nSi no solicitaste este cambio, ignora este mensaje.\n\nSaludos,\nEl equipo de Home+";

                    if ($mail->send()) {
                        $_SESSION['success'] = "✅ Hemos enviado un enlace de recuperación a tu correo electrónico.";
                    } else {
                        throw new Exception('No se pudo enviar el correo');
                    }
                    
                } catch (Exception $e) {
                    error_log("Error PHPMailer: " . $mail->ErrorInfo);
                    $_SESSION['error'] = "❌ Error al enviar el correo. Por favor, intenta nuevamente.";
                }
            } else {
                $_SESSION['error'] = "❌ Error al generar el token de recuperación.";
            }
        } else {
            $_SESSION['error'] = "❌ No se encontró una cuenta con ese correo electrónico.";
        }

    } catch (Exception $e) {
        error_log("Error en recuperación: " . $e->getMessage());
        $_SESSION['error'] = "Error en el servidor. Inténtalo más tarde.";
    }

    header("Location: /Views/modulo-usuarios/HomePlusFull/recuperarContraseña.php");
    exit();
}
?>