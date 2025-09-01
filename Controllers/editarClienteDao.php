<?php
require_once '../Model/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "acceso no permitido";
    exit;
}

$nombre = $_POST["nombre"];
$apellido = $_POST["apellido"];
$fecha = $_POST["fecha"];
$documento = $_POST["documento"];
$telefono = $_POST["telefono"];
$correo = $_POST["correo"];
$direccion = $_POST["direccion"];
$contrasena = $_post["contrasena"];



$sql = "insert into usuarios (nombre. apellido, fecha, documento) VALUES ('$nombre', '$apellido', '$documento', '$telefono', '$correo', '$direccion', '$contrasena' )";

if ($conn->query($sql) === TRUE);
