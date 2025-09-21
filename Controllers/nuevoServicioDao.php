<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido, use POST"]);
    exit;
}

require_once("../Model/database.php");

try {
    $db = new Database();
    $conn = $db->conn;

    // Validar campos obligatorios
    $required = ['titulo', 'urgencia', 'descripcion', 'direccion', 'barrio', 'servicio', 'precio'];
    foreach ($required as $r) {
        if (!isset($_POST[$r]) || trim($_POST[$r]) === '') {
            echo json_encode(["success" => false, "message" => "Falta el campo requerido: $r"]);
            exit;
        }
    }

    // Mapear campos
    $titulo = trim($_POST['titulo']);
    $urgencia = trim($_POST['urgencia']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_preferida = !empty($_POST['fecha_preferida']) ? trim($_POST['fecha_preferida']) : null;
    $hora_preferida = !empty($_POST['hora_preferida']) ? trim($_POST['hora_preferida']) : null;
    $direccion = trim($_POST['direccion']);
    $barrio = trim($_POST['barrio']);
    $referencias = trim($_POST['referencias'] ?? '');
    $precio = floatval($_POST['precio']); // 👈 aquí tomamos el precio

    // servicio → ID
    $servicio_raw = $_POST['servicio'];
    if (is_numeric($servicio_raw)) {
        $id_tipo_servicio = intval($servicio_raw);
    } else {
        $map = [
            'plomeria' => 1,
            'electricidad' => 2,
            'carpinteria' => 3,
            'pintura' => 4,
            'limpieza' => 5,
            'jardineria' => 6
        ];
        $key = strtolower(trim($servicio_raw));
        $id_tipo_servicio = $map[$key] ?? null;

        if ($id_tipo_servicio === null) {
            $q = $conn->prepare("SELECT id_tipo_servicio FROM tipo_servicio WHERE nombre = ? LIMIT 1");
            if ($q) {
                $q->bind_param("s", $servicio_raw);
                $q->execute();
                $q->bind_result($found_id);
                if ($q->fetch()) $id_tipo_servicio = $found_id;
                $q->close();
            }
        }
    }

    if ($id_tipo_servicio === null || $id_tipo_servicio === 0) {
        echo json_encode(["success" => false, "message" => "No se pudo determinar id_tipo_servicio"]);
        exit;
    }

    $id_cliente = $_SESSION['id_cliente'] ?? ($_POST['id_cliente'] ?? 1);

    //INSERT
    $sql = "INSERT INTO solicitud 
        (titulo_servicio, descripcion, direccion_servicio, barrio, fecha_preferida, hora_preferida, urgencia, referencias, precio, id_tipo_servicio, id_cliente)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error al preparar consulta: " . $conn->error]);
        exit;
    }

    // Tipos: 8 strings, 1 double y 2 ints → ssssssssdii
    $stmt->bind_param(
        "ssssssssdii",
        $titulo,
        $descripcion,
        $direccion,
        $barrio,
        $fecha_preferida,
        $hora_preferida,
        $urgencia,
        $referencias,
        $precio,
        $id_tipo_servicio,
        $id_cliente
    );

    if ($stmt->execute()) {
        $insertedId = $conn->insert_id;
        echo json_encode(["success" => true, "message" => "Solicitud creada con precio", "id" => $insertedId]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al ejecutar INSERT: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Excepción en servidor: " . $e->getMessage()]);
}
