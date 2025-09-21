<?php
require_once __DIR__ . '/../Model/database.php';
require_once __DIR__ . '/confirmacion_agendamiento_dao.php';

// Conexión
$db = new Database();
$conn = $db->conn;

// Instanciar DAO
$dao = new ConfirmacionAgendamientoDAO($conn);
$servicios = $dao->getHistorialServiciosTerminados();

// Cargar vista
require_once __DIR__ . '/../Views/modulo-confirmacion-agendamiento/cliente.php';

// Controllers/confirmacion_agendamiento_dao.php

class ConfirmacionAgendamientoDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getHistorialServiciosTerminados() {
        $sql = "
            SELECT 
                s.id_servicio,
                so.titulo_servicio,
                so.direccion_servicio,
                s.fecha_ini,
                s.fecha_fin,
                of.precio,
                s.estado
            FROM servicio s
            LEFT JOIN agendamiento a ON s.id_agendamiento = a.id_agendamiento
            LEFT JOIN oferta of ON a.id_oferta = of.id_oferta
            LEFT JOIN solicitud so ON of.id_solicitud = so.id_solicitud
            WHERE s.estado = 'Terminado' OR s.estado = 'Finalizado'
            ORDER BY s.fecha_fin DESC
        ";
        $result = $this->conn->query($sql);
        $rows = [];
        if ($result) {
            while ($r = $result->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }
}
