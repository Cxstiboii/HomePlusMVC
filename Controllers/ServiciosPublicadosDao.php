<?php
require_once __DIR__ . '/../Model/database.php';
$db = new Database(); // instanciamos la clase
$conn = $db->conn;    // obtenemos la conexión mysqli

class ServiciosPublicadosDao{
    private $conn;

    public function __construct(){
        $this->conn = (new Database())->conn;
    }

    public function obtenerServiciosPublicados(){
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
                s.estado,
                u.Nombres,
                u.Apellidos,
                e.ruta_archivo AS foto_principal
            FROM solicitud s
            JOIN cliente c ON s.id_cliente = c.id_cliente
            JOIN usuario u ON c.id_cliente = u.id_Usuario
            LEFT JOIN evidencias e ON e.id_solicitud = s.id_solicitud
            WHERE s.estado = 'Pendiente'
            GROUP BY s.id_solicitud
            ORDER BY s.fecha_solicitud DESC
        ";

        $result = $this->conn->query($sql);

        if ($result === false){
            return []; // Retorna vacio si hubo un error
        }

        $servicios = [];
        while ($row  = $result->fetch_assoc()) {
            $servicios[] = $row;
        }
        return $servicios;
    }
}
?>
