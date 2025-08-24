<?php
session_start();


$usuarios = [
    ["email" => "cliente@homeplus.com", "password" => "1234", "rol" => "cliente"],
    ["email" => "pro@homeplus.com", "password" => "5678", "rol" => "profesional"]
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email) || empty($password)) {
        header("Location: login.html?error=Completa todos los campos.");
        exit;
    }

    $usuarioValido = null;
    foreach ($usuarios as $usuario) {
        if ($usuario["email"] === $email && $usuario["password"] === $password) {
            $usuarioValido = $usuario;
            break;
        }
    }

    if ($usuarioValido) {
        $_SESSION["rol"] = $usuarioValido["rol"];

        if ($usuarioValido["rol"] === "cliente") {
            header("Location: /HomePlusMVC-main/Views/modulo-confirmacion-agendamiento/cliente.html");
            exit;
        } elseif ($usuarioValido["rol"] === "profesional") {
            header("Location: /HomePlusMVC-main/Views/modulo-servicios-publicados/index.html");
            exit;
        }
    } else {
        header("Location: login.html?error=Correo o contraseña incorrectos.");
        exit;
    }
}
