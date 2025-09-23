<?php
// Controllers/NegociarOfertaDao.php
session_start();
require_once '../Model/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSolicitud = $_POST['id_solicitud'] ?? null;
    $idProfesional = $_POST['id_profesional'] ?? null;
    $precioEstimado = $_POST['precio_estimado'] ?? null;
    $tiempoEstimado = $_POST['tiempo_estimado'] ?? null;
    $acompanante = isset($_POST['acompanante']) ? 1 : 0;
    $materialTipo = $_POST['material_tipo'] ?? [];
    $materialCantidad = $_POST['material_cantidad'] ?? [];
    $materialUnidad = $_POST['material_unidad'] ?? [];

    if (!$idSolicitud || !$idProfesional || !$precioEstimado) {
        die("Datos incompletos.");
    }

    $database = new Database();
    $db = $database->conn;

    // Insertar o actualizar la oferta
    $sql = "INSERT INTO oferta (id_solicitud, id_profesional, precio_estimado, tiempo_estimado, acompanante)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            precio_estimado=VALUES(precio_estimado),
            tiempo_estimado=VALUES(tiempo_estimado),
            acompanante=VALUES(acompanante)";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iidii", $idSolicitud, $idProfesional, $precioEstimado, $tiempoEstimado, $acompanante);
    $stmt->execute();

    $idOferta = $db->insert_id;

    // Borrar materiales anteriores
    $stmtDel = $db->prepare("DELETE FROM material WHERE id_oferta = ?");
    $stmtDel->bind_param("i", $idOferta);
    $stmtDel->execute();

    // Insertar materiales nuevos
    $stmtMat = $db->prepare("INSERT INTO material (id_oferta, tipo, cantidad, unidad) VALUES (?, ?, ?, ?)");
    for ($i = 0; $i < count($materialTipo); $i++) {
        if (!empty($materialTipo[$i])) {
            $tipo = $materialTipo[$i];
            $cantidad = $materialCantidad[$i] ?? 0;
            $unidad = $materialUnidad[$i] ?? '';
            $stmtMat->bind_param("isis", $idOferta, $tipo, $cantidad, $unidad);
            $stmtMat->execute();
        }
    }

    header("Location: ../../Views/modulo-detalles-servicio/detalles.php?id_solicitud=$idSolicitud&success=1");
    exit;
} 
?>
