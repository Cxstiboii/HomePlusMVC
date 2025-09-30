<?php
session_start();
require_once '../Model/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit;
}

// Inputs (tu código actual...)
$idSolicitud   = isset($_POST['id_solicitud']) ? intval($_POST['id_solicitud']) : 0;
$idProfesional = $_SESSION['id_Usuario'] ?? null;
$precio        = isset($_POST['precio_estimado']) ? floatval($_POST['precio_estimado']) : null;
$tiempo        = !empty($_POST['tiempo_estimado']) ? intval($_POST['tiempo_estimado']) : null;
$acompanante   = isset($_POST['acompanante']) ? 1 : 0;

$materialTipos      = $_POST['material_tipo'] ?? [];
$materialCantidades = $_POST['material_cantidad'] ?? [];
$materialUnidades   = $_POST['material_unidad'] ?? [];

// Validaciones (tu código actual...)
if (!$idSolicitud || !$idProfesional || $precio === null) {
    echo json_encode(["success" => false, "error" => "Datos incompletos."]);
    exit;
}

if ($precio <= 0) {
    echo json_encode(["success" => false, "error" => "El precio debe ser mayor a 0."]);
    exit;
}

$database = new Database();
$db = $database->conn;

try {
    $db->begin_transaction();

    // Verificar que la solicitud existe y está en estado Pendiente
    $checkStmt = $db->prepare("SELECT estado, id_profesional FROM solicitud WHERE id_solicitud = ?");
    $checkStmt->bind_param("i", $idSolicitud);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("La solicitud no existe.");
    }
    
    $solicitud = $checkResult->fetch_assoc();
    
    // ✅ ACEPTAR SOLO ESTADOS "Publicado" o "Pendiente"
    if (!in_array($solicitud['estado'], ['Publicado', 'Pendiente'])) {
        throw new Exception("Esta solicitud no está disponible para negociación. Estado actual: " . $solicitud['estado']);
    }
    
    // Verificar si ya tiene profesional asignado
    if ($solicitud['id_profesional'] !== null && $solicitud['id_profesional'] != $idProfesional) {
        throw new Exception("Esta solicitud ya tiene un profesional asignado.");
    }
    
    $checkStmt->close();

    // Insertar oferta
    $stmt = $db->prepare("INSERT INTO oferta (id_solicitud, id_profesional, precio_estimado, tiempo_estimado, acompanante, estado) 
                            VALUES (?, ?, ?, ?, ?, 'pendiente')");
    
    // Convertir horas a formato datetime
    $tiempoStr = null;
    if ($tiempo) {
        $tiempoStr = date('Y-m-d H:i:s', strtotime("+$tiempo hours"));
    }
    
    $stmt->bind_param("iidsi", $idSolicitud, $idProfesional, $precio, $tiempoStr, $acompanante);

    if (!$stmt->execute()) {
        throw new Exception("Error al insertar oferta: " . $stmt->error);
    }
    $idOferta = $db->insert_id;
    $stmt->close();

    // Insertar materiales si existen
    if (!empty($materialTipos)) {
        $stmtM = $db->prepare("INSERT INTO material (id_oferta, tipo, cantidad, unidad) VALUES (?, ?, ?, ?)");
        
        foreach ($materialTipos as $i => $tipo) {
            $tipo = trim($tipo);
            // Saltar campos vacíos
            if ($tipo === '') continue;
            
            $cant = intval($materialCantidades[$i] ?? 0);
            $unidad = trim($materialUnidades[$i] ?? '');
            
            if ($cant > 0 && $unidad !== '') {
                $stmtM->bind_param("isis", $idOferta, $tipo, $cant, $unidad);
                if (!$stmtM->execute()) {
                    throw new Exception("Error al insertar material: " . $stmtM->error);
                }
            }
        }
        $stmtM->close();
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "mensaje" => "Oferta enviada correctamente. Espera a que el cliente revise tu propuesta.",
        "idOferta" => $idOferta,
        "idSolicitud" => $idSolicitud
    ]);

} catch (Exception $e) {
    $db->rollback();
    error_log("Error en NegociarServicioDao: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>