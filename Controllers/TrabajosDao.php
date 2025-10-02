<?php
require_once __DIR__ . '/../Model/database.php';

class TrabajosDao {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    public function obtenerTrabajos($idProfesional, $estado = "Todos") {
        // Solo trabajos por oferta (NO existen trabajos directos sin id_profesional en solicitud)
        $sql = "
            SELECT DISTINCT
                s.id_solicitud,
                s.titulo_servicio,
                s.descripcion,
                s.urgencia,
                s.fecha_preferida,
                s.hora_preferida,
                s.direccion_servicio,
                s.barrio,
                s.referencias,
                s.precio as precio_original,
                s.fecha_solicitud,
                u.Nombres,
                u.Apellidos,
                u.Telefono,
                u.Email,
                o.id_oferta,
                o.precio_estimado,
                o.tiempo_estimado,
                o.acompanante,
                o.estado as estado_oferta,
                CASE 
                    WHEN o.estado = 'Negociado' THEN 'Negociado'
                    WHEN o.estado = 'Aceptado' AND s.estado = 'Agendado' THEN 'Agendado'
                    WHEN s.estado = 'Cancelado' THEN 'Cancelado'
                    WHEN o.estado = 'Aceptado' THEN 'Aceptado'
                    ELSE COALESCE(o.estado, s.estado)
                END as estado,
                'oferta' as origen
            FROM oferta o
            INNER JOIN solicitud s ON o.id_solicitud = s.id_solicitud
            INNER JOIN cliente c ON s.id_cliente = c.id_cliente
            INNER JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE o.id_profesional = ?
        ";

        // Agregar filtro por estado si no es "Todos"
        if ($estado !== "Todos" && $estado !== "todos") {
            if ($estado === "Aceptado") {
                $sql .= " AND (o.estado = 'Aceptado' OR (o.estado = 'Aceptado' AND s.estado = 'Agendado'))";
            } else if ($estado === "Agendado") {
                $sql .= " AND o.estado = 'Aceptado' AND s.estado = 'Agendado'";
            } else if ($estado === "Negociado") {
                $sql .= " AND o.estado = 'Negociado'";
            } else {
                $sql .= " AND (o.estado = ? OR s.estado = ?)";
            }
        }

        // Ordenar por fecha
        $sql .= " ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($sql);
        
        if ($estado !== "Todos" && $estado !== "todos") {
            if (in_array($estado, ['Aceptado', 'Negociado', 'Agendado'])) {
                $stmt->bind_param("i", $idProfesional);
            } else {
                $stmt->bind_param("iss", $idProfesional, $estado, $estado);
            }
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

    public function obtenerEstadisticasTrabajos($idProfesional) {
        // Solo estadísticas de ofertas
        $sql = "
            SELECT 
                CASE 
                    WHEN o.estado = 'Negociado' THEN 'Negociado'
                    WHEN o.estado = 'Aceptado' AND s.estado = 'Agendado' THEN 'Agendado'
                    WHEN s.estado = 'Cancelado' THEN 'Cancelado'
                    WHEN o.estado = 'Aceptado' THEN 'Aceptado'
                    ELSE COALESCE(o.estado, s.estado)
                END as estado,
                COUNT(*) as cantidad
            FROM oferta o
            INNER JOIN solicitud s ON o.id_solicitud = s.id_solicitud
            WHERE o.id_profesional = ?
            GROUP BY 
                CASE 
                    WHEN o.estado = 'Negociado' THEN 'Negociado'
                    WHEN o.estado = 'Aceptado' AND s.estado = 'Agendado' THEN 'Agendado'
                    WHEN s.estado = 'Cancelado' THEN 'Cancelado'
                    WHEN o.estado = 'Aceptado' THEN 'Aceptado'
                    ELSE COALESCE(o.estado, s.estado)
                END
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idProfesional);
        $stmt->execute();
        $result = $stmt->get_result();

        $estadisticas = [];
        while ($row = $result->fetch_assoc()) {
            $estadisticas[$row['estado']] = $row['cantidad'];
        }

        return $estadisticas;
    }

    /**
     * Obtiene solo trabajos que vienen de ofertas realizadas por el profesional
     */
    public function obtenerTrabajosPorOferta($idProfesional, $estado = null) {
        $sql = "
            SELECT DISTINCT
                s.id_solicitud,
                s.titulo_servicio,
                s.descripcion,
                s.urgencia,
                s.fecha_preferida,
                s.hora_preferida,
                s.direccion_servicio,
                s.barrio,
                s.referencias,
                s.precio as precio_original,
                s.fecha_solicitud,
                u.Nombres,
                u.Apellidos,
                u.Telefono,
                u.Email,
                o.id_oferta,
                o.precio_estimado,
                o.tiempo_estimado,
                o.acompanante,
                o.estado as estado_oferta,
                CASE 
                    WHEN o.estado = 'Negociado' THEN 'Negociado'
                    WHEN o.estado = 'Aceptado' AND s.estado = 'Agendado' THEN 'Agendado'
                    WHEN s.estado = 'Cancelado' THEN 'Cancelado'
                    WHEN o.estado = 'Aceptado' THEN 'Aceptado'
                    ELSE COALESCE(o.estado, s.estado)
                END as estado,
                'oferta' as origen
            FROM oferta o
            INNER JOIN solicitud s ON o.id_solicitud = s.id_solicitud
            INNER JOIN cliente c ON s.id_cliente = c.id_cliente
            INNER JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE o.id_profesional = ?
        ";

        if ($estado && $estado !== "Todos") {
            if ($estado === "Aceptado") {
                $sql .= " AND (o.estado = 'Aceptado' OR (o.estado = 'Aceptado' AND s.estado = 'Agendado'))";
            } elseif ($estado === "Negociado") {
                $sql .= " AND o.estado = 'Negociado'";
            } elseif ($estado === "Agendado") {
                $sql .= " AND o.estado = 'Aceptado' AND s.estado = 'Agendado'";
            } else {
                $sql .= " AND (o.estado = ? OR s.estado = ?)";
            }
        }

        $sql .= " ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($sql);
        
        if ($estado && $estado !== "Todos") {
            if (in_array($estado, ['Aceptado', 'Negociado', 'Agendado'])) {
                $stmt->bind_param("i", $idProfesional);
            } else {
                $stmt->bind_param("iss", $idProfesional, $estado, $estado);
            }
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

    /**
     * Obtiene solo trabajos asignados directamente - ELIMINADO porque no existe id_profesional en solicitud
     */
    public function obtenerTrabajosDirectos($idProfesional, $estado = null) {
        // No hay trabajos directos en esta estructura de BD
        return [];
    }

    /**
     * Verifica si un trabajo puede cambiar de estado según las reglas del negocio
     */
    public function puedeModificarEstado($idSolicitud, $estadoActual, $nuevoEstado, $idProfesional) {
        // Reglas de transición de estados
        $transicionesPermitidas = [
            'Pendiente' => ['Aceptado', 'Cancelado'],
            'Aceptado' => ['Agendado', 'Cancelado'],
            'Negociado' => ['Aceptado', 'Cancelado'],
            'Agendado' => ['En_Progreso', 'Cancelado'],
            'En_Progreso' => ['Completado', 'Cancelado']
        ];

        if (!isset($transicionesPermitidas[$estadoActual])) {
            return false;
        }

        if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoActual])) {
            return false;
        }

        // Verificar que el profesional tenga permisos para modificar esta solicitud
        return $this->tienePermisosModificacion($idSolicitud, $idProfesional);
    }

    /**
     * Verifica si el profesional tiene permisos para modificar una solicitud
     */
    private function tienePermisosModificacion($idSolicitud, $idProfesional) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM oferta o 
            WHERE o.id_solicitud = ? 
            AND o.id_profesional = ?
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idSolicitud, $idProfesional);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }

    /**
     * Método adicional para debugging - mostrar origen de trabajos
     */
    public function obtenerTrabajosPorOrigen($idProfesional) {
        $trabajosOfertas = $this->obtenerTrabajosPorOferta($idProfesional);
        
        return [
            'ofertas' => $trabajosOfertas,
            'directos' => [],
            'total_ofertas' => count($trabajosOfertas),
            'total_directos' => 0
        ];
    }
}
?>