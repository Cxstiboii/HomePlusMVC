<?php
session_start();
require_once __DIR__ . '/../Model/database.php';

// Verificar que el usuario esté autenticado y sea profesional
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_profesional = $_SESSION['id_usuario'];

try {
    // Crear conexión MySQLi (como en tu DAO)
    $db = new Database();
    $conn = $db->conn;

    // 1. Obtener información básica del profesional
    $sql_profesional = "SELECT u.*, p.especialidad, p.experiencia, p.calificaciones as calif_promedio
                        FROM usuario u
                        INNER JOIN profesional p ON u.id_Usuario = p.id_Profesional
                        WHERE u.id_Usuario = ?";
    
    $stmt = $conn->prepare($sql_profesional);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $profesional = $result->fetch_assoc();
    
    if (!$profesional) {
        die("Profesional no encontrado");
    }

    // 2. Obtener estadísticas generales
    $sql_stats = "SELECT 
                    COUNT(DISTINCT o.id_oferta) as total_ofertas,
                    COUNT(DISTINCT CASE WHEN o.estado = 'Aceptada' THEN o.id_oferta END) as ofertas_aceptadas,
                    COUNT(DISTINCT s.id_servicio) as servicios_completados,
                    COUNT(DISTINCT CASE WHEN s.estado = 'En progreso' THEN s.id_servicio END) as servicios_progreso,
                    COALESCE(SUM(o.precio), 0) as ingresos_totales,
                    COALESCE(AVG(c.puntuacion), 0) as calificacion_promedio
                  FROM profesional p
                  LEFT JOIN oferta o ON p.id_Profesional = o.id_profesional
                  LEFT JOIN agendamiento a ON o.id_oferta = a.id_oferta
                  LEFT JOIN servicio s ON a.id_agendamiento = s.id_agendamiento
                  LEFT JOIN calificacion c ON p.id_Profesional = c.id_profesional
                  WHERE p.id_Profesional = ?";
    
    $stmt = $conn->prepare($sql_stats);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    // 3. Obtener servicios completados con detalles
    $sql_completados = "SELECT 
                          s.id_servicio,
                          sol.titulo_servicio,
                          ts.tp_tipoServicio as tipo_servicio,
                          o.precio,
                          c.puntuacion,
                          c.comentario,
                          CONCAT(u.Nombres, ' ', u.Apellidos) as nombre_cliente,
                          s.fecha_ini,
                          s.fecha_fin
                        FROM servicio s
                        INNER JOIN agendamiento a ON s.id_agendamiento = a.id_agendamiento
                        INNER JOIN oferta o ON a.id_oferta = o.id_oferta
                        INNER JOIN solicitud sol ON o.id_solicitud = sol.id_solicitud
                        INNER JOIN tipo_servicio ts ON sol.id_tipo_servicio = ts.id_tipo_servicio
                        INNER JOIN cliente cli ON sol.id_cliente = cli.id_cliente
                        INNER JOIN usuario u ON cli.id_cliente = u.id_Usuario
                        LEFT JOIN calificacion c ON (s.id_servicio = c.id_profesional AND c.id_cliente = cli.id_cliente)
                        WHERE o.id_profesional = ? 
                        AND s.estado = 'Completado'
                        ORDER BY s.fecha_fin DESC";
    
    $stmt = $conn->prepare($sql_completados);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicios_completados = [];
    while ($row = $result->fetch_assoc()) {
        $servicios_completados[] = $row;
    }

    // 4. Obtener servicios en progreso
    $sql_progreso = "SELECT 
                       sol.titulo_servicio,
                       ts.tp_tipoServicio as tipo_servicio,
                       o.precio,
                       CONCAT(u.Nombres, ' ', u.Apellidos) as nombre_cliente,
                       a.fecha_asignacion,
                       a.hora_ini,
                       s.fecha_ini
                     FROM servicio s
                     INNER JOIN agendamiento a ON s.id_agendamiento = a.id_agendamiento
                     INNER JOIN oferta o ON a.id_oferta = o.id_oferta
                     INNER JOIN solicitud sol ON o.id_solicitud = sol.id_solicitud
                     INNER JOIN tipo_servicio ts ON sol.id_tipo_servicio = ts.id_tipo_servicio
                     INNER JOIN cliente cli ON sol.id_cliente = cli.id_cliente
                     INNER JOIN usuario u ON cli.id_cliente = u.id_Usuario
                     WHERE o.id_profesional = ? 
                     AND s.estado = 'En progreso'
                     ORDER BY a.fecha_asignacion DESC";
    
    $stmt = $conn->prepare($sql_progreso);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicios_progreso = [];
    while ($row = $result->fetch_assoc()) {
        $servicios_progreso[] = $row;
    }

    // 5. Obtener servicios agendados
    $sql_agendados = "SELECT 
                        sol.titulo_servicio,
                        ts.tp_tipoServicio as tipo_servicio,
                        o.precio,
                        CONCAT(u.Nombres, ' ', u.Apellidos) as nombre_cliente,
                        a.fecha_asignacion,
                        a.hora_ini
                      FROM agendamiento a
                      INNER JOIN oferta o ON a.id_oferta = o.id_oferta
                      INNER JOIN solicitud sol ON o.id_solicitud = sol.id_solicitud
                      INNER JOIN tipo_servicio ts ON sol.id_tipo_servicio = ts.id_tipo_servicio
                      INNER JOIN cliente cli ON sol.id_cliente = cli.id_cliente
                      INNER JOIN usuario u ON cli.id_cliente = u.id_Usuario
                      WHERE o.id_profesional = ? 
                      AND a.estado = 'Agendado'
                      AND NOT EXISTS (SELECT 1 FROM servicio s WHERE s.id_agendamiento = a.id_agendamiento)
                      ORDER BY a.fecha_asignacion DESC";
    
    $stmt = $conn->prepare($sql_agendados);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicios_agendados = [];
    while ($row = $result->fetch_assoc()) {
        $servicios_agendados[] = $row;
    }

    // 6. Obtener resumen por tipo de servicio
    $sql_resumen = "SELECT 
                      ts.tp_tipoServicio as tipo,
                      COUNT(*) as total_servicios,
                      SUM(o.precio) as total_ingresos
                    FROM servicio s
                    INNER JOIN agendamiento a ON s.id_agendamiento = a.id_agendamiento
                    INNER JOIN oferta o ON a.id_oferta = o.id_oferta
                    INNER JOIN solicitud sol ON o.id_solicitud = sol.id_solicitud
                    INNER JOIN tipo_servicio ts ON sol.id_tipo_servicio = ts.id_tipo_servicio
                    WHERE o.id_profesional = ? 
                    AND s.estado = 'Completado'
                    GROUP BY ts.tp_tipoServicio
                    ORDER BY total_ingresos DESC";
    
    $stmt = $conn->prepare($sql_resumen);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $resumen_servicios = [];
    while ($row = $result->fetch_assoc()) {
        $resumen_servicios[] = $row;
    }

    // 7. Obtener clientes mejor calificadores
    $sql_clientes = "SELECT 
                       CONCAT(u.Nombres, ' ', u.Apellidos) as nombre_cliente,
                       COUNT(DISTINCT s.id_servicio) as servicios_realizados,
                       AVG(c.puntuacion) as calificacion_promedio,
                       MAX(c.comentario) as ultimo_comentario
                     FROM servicio s
                     INNER JOIN agendamiento a ON s.id_agendamiento = a.id_agendamiento
                     INNER JOIN oferta o ON a.id_oferta = o.id_oferta
                     INNER JOIN solicitud sol ON o.id_solicitud = sol.id_solicitud
                     INNER JOIN cliente cli ON sol.id_cliente = cli.id_cliente
                     INNER JOIN usuario u ON cli.id_cliente = u.id_Usuario
                     LEFT JOIN calificacion c ON cli.id_cliente = c.id_cliente AND o.id_profesional = c.id_profesional
                     WHERE o.id_profesional = ? 
                     AND s.estado = 'Completado'
                     AND c.puntuacion IS NOT NULL
                     GROUP BY u.id_Usuario, u.Nombres, u.Apellidos
                     HAVING AVG(c.puntuacion) >= 4
                     ORDER BY calificacion_promedio DESC, servicios_realizados DESC
                     LIMIT 5";
    
    $stmt = $conn->prepare($sql_clientes);
    $stmt->bind_param("i", $id_profesional);
    $stmt->execute();
    $result = $stmt->get_result();
    $mejores_clientes = [];
    while ($row = $result->fetch_assoc()) {
        $mejores_clientes[] = $row;
    }

    // Calcular el máximo de ingresos para las barras de progreso
    $max_ingresos = 0;
    foreach ($resumen_servicios as $resumen) {
        if ($resumen['total_ingresos'] > $max_ingresos) {
            $max_ingresos = $resumen['total_ingresos'];
        }
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    die("Error en la consulta: " . $e->getMessage());
}

// Incluir la vista
require_once '../Views/modulo-confirmacion-agendamiento/reporteProfesional.php';
?>