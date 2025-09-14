<?php
require_once "../Model/database.php";

class nuevoServicioDao
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    public function crearSolicitud($titulo, $urgencia, $descripcion, $fecha, $hora, $direccion, $barrio, $referencias, $servicio)
    {
        $sql = "INSERT INTO solicitud 
                (titulo_servicio, urgencia, descripcion, fecha_preferida, hora_preferida, direccion_servicio, barrio, referencias, servicio) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die(json_encode([
                "success" => false,
                "message" => "❌ Error en la consulta: " . $this->db->error
            ]));
        }

        $stmt->bind_param(
            "sssssssss",
            $titulo,
            $urgencia,
            $descripcion,
            $fecha,
            $hora,
            $direccion,
            $barrio,
            $referencias,
            $servicio
        );

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Solicitud registrada correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al registrar: " . $stmt->error]);
        }

        $stmt->close();
    }
}

// Controlador que recibe los datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dao = new nuevoServicioDao();

    $dao->crearSolicitud(
        $_POST["titulo_servicio"] ?? "",
        $_POST["urgencia"] ?? "",
        $_POST["descripcion"] ?? "",
        $_POST["fecha_preferida"] ?? null,
        $_POST["hora_preferida"] ?? null,
        $_POST["direccion_servicio"] ?? "",
        $_POST["barrio"] ?? "",
        $_POST["referencias"] ?? "",
        $_POST["servicio"] ?? ""
    );
}
