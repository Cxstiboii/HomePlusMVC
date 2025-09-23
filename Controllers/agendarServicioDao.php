<?php
session_start();
require_once '../Model/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idSolicitud = $_POST['id_solicitud'] ?? null;
    $idProfesional = $_SESSION['id_Usuario'] ?? null;

    if (!$idSolicitud || !$idProfesional) {
        echo json_encode(["error" => "Datos incompletos."]);
        exit;
    }

    $database = new Database();
    $db = $database->conn;

    try {
        // Iniciar transacción para garantizar consistencia
        $db->begin_transaction();

        // Debug temporal - puedes eliminar después
        error_log("DEBUG - ID Solicitud: " . $idSolicitud);
        error_log("DEBUG - ID Profesional: " . $idProfesional);

        // 1. Crear agendamiento (CON id_solicitud)
        $stmtAg = $db->prepare("
            INSERT INTO agendamiento (id_solicitud, id_oferta, fecha_asignacion, hora_ini, descripcion, estado) 
            VALUES (?, NULL, NOW(), NOW(), 'Servicio agendado por el profesional', 'agendado')
        ");
        $stmtAg->bind_param("i", $idSolicitud);
        $stmtAg->execute();
        $idAgendamiento = $db->insert_id;

        // Debug adicional
        error_log("DEBUG - ID Agendamiento creado: " . $idAgendamiento);

        if (!$idAgendamiento) {
            throw new Exception("Error al crear el agendamiento");
        }

        // 2. Crear servicio asociado
        $stmtServ = $db->prepare("
            INSERT INTO servicio (id_agendamiento, fecha_ini, estado) 
            VALUES (?, NOW(), 'activo')
        ");
        $stmtServ->bind_param("i", $idAgendamiento);
        $stmtServ->execute();

        if ($stmtServ->affected_rows === 0) {
            throw new Exception("Error al crear el servicio");
        }

        // 3. Actualizar solicitud: asignar profesional y cambiar estado
        $stmtSol = $db->prepare("
            UPDATE solicitud 
            SET estado = 'Agendado', id_profesional = ? 
            WHERE id_solicitud = ?
        ");
        $stmtSol->bind_param("ii", $idProfesional, $idSolicitud);
        $stmtSol->execute();

        if ($stmtSol->affected_rows === 0) {
            throw new Exception("Error al actualizar la solicitud o solicitud no encontrada");
        }

        // Confirmar transacción
        $db->commit();

        // 4. Retornar respuesta exitosa
        echo json_encode([
            "success" => true,
            "mensaje" => "Servicio agendado con éxito",
            "estado" => "Agendado",
            "idProfesional" => $idProfesional,
            "idAgendamiento" => $idAgendamiento,
            "idSolicitud" => $idSolicitud
        ]);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $db->rollback();
        
        echo json_encode([
            "success" => false,
            "error" => "Error al agendar: " . $e->getMessage()
        ]);
    } finally {
        // Cerrar conexión
        $db->close();
    }

} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>