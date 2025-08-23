<?php
// Parámetros de conexión
$host = "localhost";   // o la IP del servidor
$usuario = "root";     // tu usuario de MySQL
$clave = "";           // tu contraseña de MySQL
$bd = "home_usuarios_vacia"; // nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $clave, $bd);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
} else {
    echo "✅ Conexión exitosa a la base de datos";
}
?>
