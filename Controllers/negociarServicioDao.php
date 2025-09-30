<?php
session_start();
require_once '../Model/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit;
}

// Inputs
$idSolicitud   = isset($_POST['id_solicitud']) ? intval($_POST['id_solicitud']) : 0;
$idProfesional = $_SESSION['id_Usuario'] ?? null;
$precio        = isset($_POST['precio_estimado']) ? floatval($_POST['precio_estimado']) : null;
$tiempo        = !empty($_POST['tiempo_estimado']) ? intval($_POST['tiempo_estimado']) : null;
$acompanante   = isset($_POST['acompanante']) ? 1 : 0;

$materialTipos      = $_POST['material_tipo'] ?? [];
$materialCantidades = $_POST['material_cantidad'] ?? [];
$materialUnidades   = $_POST['material_unidad'] ?? [];

// Validaciones
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

    // Actualizar estado de la solicitud a "Negociado" y asignar profesional
    $updateStmt = $db->prepare("UPDATE solicitud SET estado = 'Negociado', id_profesional = ? WHERE id_solicitud = ?");
    $updateStmt->bind_param("ii", $idProfesional, $idSolicitud);
    if (!$updateStmt->execute()) {
        throw new Exception("Error al actualizar solicitud: " . $updateStmt->error);
    }
    $updateStmt->close();

    $db->commit();

    echo json_encode([
        "success" => true,
        "mensaje" => "Oferta enviada correctamente. El servicio ahora aparece en 'Mis Trabajos' como Negociado.",
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