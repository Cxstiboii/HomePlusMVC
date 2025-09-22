<?php
require_once __DIR__ . '/../Model/database.php';

class TrabajosDao {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->conn; 
    }

    /**
     * Obtiene los trabajos del profesional logueado según su estado.
     * 
     * @param int $idProfesional ID del profesional logueado.
     * @param string|null $estado Estado del trabajo (Pendiente, Aceptada, Negociada, Agendada, Cancelada).
     *                            Si es null o "Todos", trae todos los estados.
     * @return array Lista de trabajos.
     */
    public function obtenerTrabajos($idProfesional, $estado = null) {
        $sql = "
            SELECT 
                s.id_solicitud,
                s.titulo_servicio,
                s.descripcion,
                s.urgencia,
                s.fecha_preferida,
                s.hora_preferida,
                s.direccion_servicio,
                s.barrio,
                s.referencias,
                u.Nombres,
                u.Apellidos,
                o.estado AS estado
            FROM oferta o
            JOIN solicitud s ON o.id_solicitud = s.id_solicitud
            JOIN cliente c ON s.id_cliente = c.id_cliente
            JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE o.id_profesional = ?
        ";

        // Filtrar por estado si se especifica
        if ($estado !== null && $estado !== "Todos") {
            $sql .= " AND o.estado = ?";
        }

        $sql .= " ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($sql);

        if ($estado !== null && $estado !== "Todos") {
            $stmt->bind_param("is", $idProfesional, $estado);
        } else {
            $stmt->bind_param("i", $idProfesional);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $trabajos = [];
        while ($row = $result->fetch_assoc()) {
            $trabajos[] = $row;
        }

        return $trabajos;
    }
}
