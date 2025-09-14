<?php
require_once __DIR__ . '/../Model/database.php';

class TrabajosAceptadosDao {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->conn; // Asumiendo que Database devuelve $conn
    }

    public function obtenerTrabajosAceptados() {
        $sql = "
            SELECT s.id_solicitud, s.titulo_servicio, s.urgencia, s.fecha_preferida, s.estado, 
                    u.Nombres, u.Apellidos, s.direccion_servicio, s.barrio, s.referencias, s.descripcion, s.hora_preferida
            FROM solicitud s
            JOIN usuario u ON s.id_cliente = u.id_Usuario
            WHERE s.estado = 'Aceptado'
            ORDER BY s.fecha_solicitud DESC
        ";

        $result = $this->conn->query($sql);

        if ($result === false) {
            return []; // Retorna arreglo vacío si hay error
        }

        $trabajos = [];
        while ($row = $result->fetch_assoc()) {
            $trabajos[] = $row;
        }

        return $trabajos;
    }
}
?>
