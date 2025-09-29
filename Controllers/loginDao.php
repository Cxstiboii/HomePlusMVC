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

        // 📧 ENVIAR NOTIFICACIÓN DE INICIO DE SESIÓN
        enviarNotificacionLogin($correo, $usuario["Nombres"], $usuario["Tipo_Usuario"]);

        // Redirigir según tipo de usuario
        if ($usuario["Tipo_Usuario"] === "cliente") {
            header("Location: /Views/modulo-confirmacion-agendamiento/cliente.php");
        } else {
            header("Location: /Views/modulo-confirmacion-agendamiento/profesional.php");
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

// 🔧 FUNCIÓN PARA ENVIAR NOTIFICACIÓN DE LOGIN
function enviarNotificacionLogin($email, $nombre, $tipoUsuario) {
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

        // Obtener información de conexión
        $ip = obtenerIP();
        $ubicacion = obtenerUbicacionAproximada($ip);
        $navegador = obtenerNavegador();
        $plataforma = obtenerPlataforma();

        // Contenido del email
        $mail->isHTML(true);
        $mail->Subject = 'Se ha iniciado sesión en tu cuenta - Home+';
        
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
                .alert-box {
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
                .warning {
                    color: #856404;
                    font-weight: bold;
                }
                .security-info {
                    background: var(--light-beige);
                    padding: 10px;
                    border-radius: 5px;
                    margin: 10px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Actividad Reciente</h1>
                </div>
                
                <p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
                
                <div class='alert-box'>
                    <h3>✅ Se ha iniciado sesión en tu cuenta</h3>
                    <p><strong>Fecha y hora:</strong> " . date('d/m/Y H:i:s') . "</p>
                    <p><strong>Tipo de cuenta:</strong> " . ucfirst($tipoUsuario) . "</p>
                </div>
                
                <div class='info-box'>
                    <h4>📋 Información del acceso:</h4>
                    
                    <div class='security-info'>
                        <strong>📍 Dirección IP:</strong> " . htmlspecialchars($ip) . "<br>
                        <strong>🌍 Ubicación aproximada:</strong> " . htmlspecialchars($ubicacion) . "<br>
                        <strong>🖥️ Navegador:</strong> " . htmlspecialchars($navegador) . "<br>
                        <strong>💻 Plataforma:</strong> " . htmlspecialchars($plataforma) . "
                    </div>
                    
                    <p><strong>• Hora:</strong> " . date('H:i:s') . "</p>
                    <p><strong>• Fecha:</strong> " . date('d/m/Y') . "</p>
                    <p><strong>• Tipo de usuario:</strong> " . ucfirst($tipoUsuario) . "</p>
                </div>
                
                <p class='warning'>⚠️ Si no reconoces esta actividad, por favor:</p>
                <ul>
                    <li>Cambia tu contraseña inmediatamente</li>
                    <li>Revisa tu cuenta en busca de actividades sospechosas</li>
                    <li>Contacta a soporte si necesitas ayuda</li>
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
                        "Se ha iniciado sesión en tu cuenta de Home+.\n\n" .
                        "Fecha: " . date('d/m/Y') . "\n" .
                        "Hora: " . date('H:i:s') . "\n" .
                        "Tipo de cuenta: " . ucfirst($tipoUsuario) . "\n\n" .
                        "Información del acceso:\n" .
                        "IP: $ip\n" .
                        "Ubicación: $ubicacion\n" .
                        "Navegador: $navegador\n" .
                        "Plataforma: $plataforma\n\n" .
                        "Si no reconoces esta actividad, cambia tu contraseña inmediatamente.\n\n" .
                        "Este es un mensaje automático de seguridad.\n" .
                        "El equipo de Home+";

        // Enviar el email
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error enviando notificación de login: " . $e->getMessage());
        return false;
    }
}

// 🔍 FUNCIÓN PARA OBTENER IP DEL USUARIO
function obtenerIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// 🗺️ FUNCIÓN PARA OBTENER UBICACIÓN APROXIMADA
function obtenerUbicacionAproximada($ip) {
    // Para IPs locales, mostrar como local
    if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
        return 'Red local (Desarrollo)';
    }
    
    // Intenta obtener ubicación de IP pública
    try {
        $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country,regionName,city");
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && $data['status'] !== 'fail') {
                $ubicacion = '';
                if (!empty($data['city'])) $ubicacion .= $data['city'] . ', ';
                if (!empty($data['regionName'])) $ubicacion .= $data['regionName'] . ', ';
                if (!empty($data['country'])) $ubicacion .= $data['country'];
                return $ubicacion ?: 'Ubicación no disponible';
            }
        }
    } catch (Exception $e) {
        // Silenciar errores de geolocalización
    }
    
    return 'Ubicación no disponible';
}

// 🌐 FUNCIÓN PARA OBTENER NAVEGADOR
function obtenerNavegador() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    
    if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
    if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
    if (strpos($user_agent, 'Safari') !== false) return 'Safari';
    if (strpos($user_agent, 'Edge') !== false) return 'Edge';
    if (strpos($user_agent, 'Opera') !== false) return 'Opera';
    
    return 'Navegador desconocido';
}

// 💻 FUNCIÓN PARA OBTENER PLATAFORMA
function obtenerPlataforma() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    
    if (strpos($user_agent, 'Windows') !== false) return 'Windows';
    if (strpos($user_agent, 'Mac') !== false) return 'macOS';
    if (strpos($user_agent, 'Linux') !== false) return 'Linux';
    if (strpos($user_agent, 'Android') !== false) return 'Android';
    if (strpos($user_agent, 'iPhone') !== false) return 'iOS';
    
    return 'Plataforma desconocida';
}
?>