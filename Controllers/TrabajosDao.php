<?php
require_once __DIR__ . '/../Model/database.php';

class TrabajosDao {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    public function obtenerTrabajos($idProfesional, $estado = "Todos") {
        // Primera consulta: trabajos por oferta (prioridad)
        $sqlOfertas = "
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
                'oferta' as origen,
                1 as prioridad
            FROM oferta o
            INNER JOIN solicitud s ON o.id_solicitud = s.id_solicitud
            INNER JOIN cliente c ON s.id_cliente = c.id_cliente
            INNER JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE o.id_profesional = ?
        ";

        // Segunda consulta: trabajos directos (solo los que NO tienen ofertas del profesional)
        $sqlDirectos = "
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
                NULL as id_oferta,
                NULL as precio_estimado,
                NULL as tiempo_estimado,
                NULL as acompanante,
                NULL as estado_oferta,
                s.estado,
                'directo' as origen,
                2 as prioridad
            FROM solicitud s
            INNER JOIN cliente c ON s.id_cliente = c.id_cliente
            INNER JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE s.id_profesional = ? 
            AND s.id_solicitud NOT IN (
                SELECT DISTINCT o2.id_solicitud 
                FROM oferta o2 
                WHERE o2.id_profesional = ?
            )
        ";

        // Unir ambas consultas
        $sql = "(" . $sqlOfertas . ") UNION (" . $sqlDirectos . ")";

        // Agregar filtro por estado si no es "Todos"
        if ($estado !== "Todos" && $estado !== "todos") {
            if ($estado === "Aceptado") {
                $sql = "SELECT * FROM (" . $sql . ") AS trabajos_unidos WHERE estado IN ('Aceptado', 'Agendado')";
            } else {
                $sql = "SELECT * FROM (" . $sql . ") AS trabajos_unidos WHERE estado = ?";
            }
        }

        // Ordenar por prioridad (ofertas primero) y luego por fecha
        $sql .= " ORDER BY prioridad ASC, fecha_solicitud DESC";

        $stmt = $this->conn->prepare($sql);

        if ($estado !== "Todos" && $estado !== "todos") {
            if ($estado === "Aceptado") {
                $stmt->bind_param("iii", $idProfesional, $idProfesional, $idProfesional);
            } else {
                $stmt->bind_param("iiis", $idProfesional, $idProfesional, $idProfesional, $estado);
            }
        } else {
            $stmt->bind_param("iii", $idProfesional, $idProfesional, $idProfesional);
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
        // Misma lógica pero para estadísticas, evitando duplicados
        $sql = "
            SELECT estado, COUNT(*) as cantidad FROM (
                (SELECT 
                    CASE 
                        WHEN o.estado = 'Negociado' THEN 'Negociado'
                        WHEN o.estado = 'Aceptado' AND s.estado = 'Agendado' THEN 'Agendado'
                        WHEN s.estado = 'Cancelado' THEN 'Cancelado'
                        WHEN o.estado = 'Aceptado' THEN 'Aceptado'
                        ELSE COALESCE(o.estado, s.estado)
                    END as estado
                FROM oferta o
                INNER JOIN solicitud s ON o.id_solicitud = s.id_solicitud
                WHERE o.id_profesional = ?)
                
                UNION
                
                (SELECT s.estado
                FROM solicitud s
                WHERE s.id_profesional = ? 
                AND s.id_solicitud NOT IN (
                    SELECT DISTINCT o2.id_solicitud 
                    FROM oferta o2 
                    WHERE o2.id_profesional = ?
                ))
            ) AS todos_estados
            GROUP BY estado
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $idProfesional, $idProfesional, $idProfesional);
        $stmt->execute();
        $result = $stmt->get_result();

        $estadisticas = [];
        while ($row = $result->fetch_assoc()) {
            $estadisticas[$row['estado']] = $row['cantidad'];
        }

        return $estadisticas;
    }

    /**
     * Obtiene solo trabajos asignados directamente (sin pasar por ofertas)
     */
    public function obtenerTrabajosDirectos($idProfesional, $estado = null) {
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
                s.estado,
                u.Nombres,
                u.Apellidos,
                u.Telefono,
                u.Email,
                'directo' as origen
            FROM solicitud s
            INNER JOIN cliente c ON s.id_cliente = c.id_cliente
            INNER JOIN usuario u ON c.id_cliente = u.id_Usuario
            WHERE s.id_profesional = ?
            AND s.id_solicitud NOT IN (
                SELECT DISTINCT o.id_solicitud 
                FROM oferta o 
                WHERE o.id_profesional = ?
            )
        ";

        if ($estado && $estado !== "Todos") {
            $sql .= " AND s.estado = ?";
        }

        $sql .= " ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($sql);
        
        if ($estado && $estado !== "Todos") {
            $stmt->bind_param("iis", $idProfesional, $idProfesional, $estado);
        } else {
            $stmt->bind_param("ii", $idProfesional, $idProfesional);
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
     * Verifica si un trabajo puede cambiar de estado según las reglas del negocio
     */
    public function puedeModificarEstado($idSolicitud, $estadoActual, $nuevoEstado, $idProfesional) {
        // Reglas de transición de estados
        $transicionesPermitidas = [
            'Pendiente' => ['Aceptado', 'Cancelado'],
            'Aceptado' => ['Agendado', 'Cancelado'],
            'Negociado' => ['Aceptado', 'Cancelado'], // Solo si el cliente acepta
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
        return $this->tienePrimisosModificacion($idSolicitud, $idProfesional);
    }

    /**
     * Verifica si el profesional tiene permisos para modificar una solicitud
     */
    private function tienePrimisosModificacion($idSolicitud, $idProfesional) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM solicitud s 
            WHERE s.id_solicitud = ? 
            AND (s.id_profesional = ? OR EXISTS(
                SELECT 1 FROM oferta o 
                WHERE o.id_solicitud = s.id_solicitud 
                AND o.id_profesional = ?
            ))
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $idSolicitud, $idProfesional, $idProfesional);
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
        $trabajosDirectos = $this->obtenerTrabajosDirectos($idProfesional);
        
        return [
            'ofertas' => $trabajosOfertas,
            'directos' => $trabajosDirectos,
            'total_ofertas' => count($trabajosOfertas),
            'total_directos' => count($trabajosDirectos)
        ];
    }
}
?>