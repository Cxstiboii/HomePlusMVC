<?php
// Controllers/ActualizarPerfilProfesionalDao.php
session_start();
require_once '../Model/database.php';
require_once 'PerfilProfesionalDao.php';

// Verificar sesión y tipo de usuario
$idUsuario = $_SESSION['id_Usuario'] ?? null;
$tipoUsuario = $_SESSION['tipo_usuario'] ?? null;

if (!$idUsuario || $tipoUsuario !== 'profesional') {
    echo "<p class='no-datos'>Acceso no autorizado. Debes ser un profesional.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dao = new PerfilProfesionalDao();

    // Recibir datos del formulario
    $data = [
        'nombres' => trim($_POST['nombres']),
        'apellidos' => trim($_POST['apellidos']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono']),
        'direccion' => trim($_POST['direccion']),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'especialidad' => trim($_POST['especialidad']),
        'historial' => trim($_POST['historial'])
    ];

    $foto = null;

    // Manejo de foto si se subió
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['foto_perfil']['tmp_name'];
        $fileName = basename($_FILES['foto_perfil']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowed)) {
            $newFileName = 'perfil_' . $idUsuario . '.' . $fileExt;

            // Ruta absoluta de tu carpeta en Windows
            $uploadDir = 'C:/Users/Samuel/Desktop/HomePlusMVC/Views/assets/uploads/img-usuarios/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                // Guardamos la ruta relativa para la BD
                $foto = '/Views/assets/uploads/img-usuarios/' . $newFileName;
            } else {
                error_log("ERROR: No se pudo mover la imagen subida.");
            }
        } else {
            error_log("ERROR: Extensión de imagen no permitida: $fileExt");
        }
    }

    // Actualizar perfil
    $resultado = $dao->actualizarPerfilProfesional($idUsuario, $data, $foto);

    if ($resultado) {
        echo "<p class='exito'>Perfil actualizado correctamente ✅</p>";
        echo "<script>setTimeout(() => { window.location.href = '../Views/modulo-perfil-profesional/perfil.php'; }, 1500);</script>";
    } else {
        echo "<p class='error'>Ocurrió un error al actualizar el perfil. Intenta nuevamente.</p>";
    }
} else {
    echo "<p class='error'>Solicitud inválida.</p>";
}
?>
