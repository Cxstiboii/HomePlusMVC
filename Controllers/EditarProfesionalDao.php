<?php
include "../Model/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $servicio = $_POST['servicio'];
    $servicioAdicional = $_POST['servicioAdicional'];
    $experiencia = $_POST['experiencia'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $tipoDocumento = $_POST['tipoDocumento'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $contrasena = $_POST['contraseña'];

    $sql = "INSERT INTO profesional
        (nombres, apellidos, servicio, servicioAdicional, experiencia, fecha_nacimiento, tipo_documento, documento, telefono, correo, direccion, `contraseña`)
        VALUES ('$nombres', '$apellidos', '$servicio', '$servicioAdicional', '$experiencia', '$fechaNacimiento', '$tipoDocumento', '$documento', '$telefono', '$email', '$direccion', '$contrasena')";

    if (mysqli_query($conn, $sql)) {
        echo "Datos guardados correctamente";
    } else {
        echo "ERROR: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
