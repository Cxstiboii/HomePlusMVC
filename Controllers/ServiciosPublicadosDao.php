<?php
require_once __DIR__ . '/../Model/database.php';

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
                s.precio,  -- 👈 AÑADE ESTA LÍNEA
                u.Nombres,
                u.Apellidos,
                ts.tp_tipoServicio,
                s.fecha_solicitud
            FROM solicitud s
            JOIN cliente c ON s.id_cliente = c.id_cliente
            JOIN usuario u ON c.id_cliente = u.id_Usuario
            JOIN tipo_servicio ts ON s.id_tipo_servicio = ts.id_tipo_servicio
            WHERE s.estado = 'Pendiente'
            ORDER BY s.fecha_solicitud DESC
        ";

        $result = $this->conn->query($sql);

        if ($result === false){
            return [];
        }

        $servicios = [];
        while ($row = $result->fetch_assoc()) {
            // Obtener las imágenes específicas para esta solicitud
            $imagenes = $this->obtenerImagenesPorSolicitud($row['id_solicitud']);
            
            $row['imagenes_array'] = $imagenes;
            $row['foto_principal'] = !empty($imagenes) ? $imagenes[0] : $this->obtenerImagenPorDefecto($row['tp_tipoServicio']);
            
            $servicios[] = $row;
        }
        return $servicios;
    }

    private function obtenerImagenesPorSolicitud($id_solicitud) {
        $sql = "
            SELECT ruta_archivo 
            FROM evidencias 
            WHERE id_solicitud = ? 
            ORDER BY id_evidencia ASC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $imagenes = [];
        while ($row = $result->fetch_assoc()) {
            // Asegurar que la ruta sea correcta
            $ruta = $row['ruta_archivo'];
            if (!empty($ruta)) {
                // Si la ruta no empieza con /Views/assets/, ajustarla
                if (strpos($ruta, '/Views/assets/') === false) {
                    $ruta = '/Views/assets/uploads/service-requests/' . basename($ruta);
                }
                $imagenes[] = $ruta;
            }
        }
        
        return $imagenes;
    }

    private function obtenerImagenPorDefecto($tipoServicio) {
        // Mapear tipos de servicio a imágenes por defecto
        $imagenesPorDefecto = [
            'Plomería' => '/Views/assets/imagenes-comunes/servicios/plomeria.jpg',
            'Electricidad' => '/Views/assets/imagenes-comunes/servicios/electricista.jpg',
            'Carpintería' => '/Views/assets/imagenes-comunes/servicios/carpintero.jpg',
            'Pintura' => '/Views/assets/imagenes-comunes/servicios/pintura.jpg',
            'Limpieza' => '/Views/assets/imagenes-comunes/servicios/limpieza.jpg',
            'Jardinería' => '/Views/assets/imagenes-comunes/servicios/jardineria.jpg'
        ];
        
        return $imagenesPorDefecto[$tipoServicio] ?? '/Views/assets/imagenes-comunes/servicios/default.jpg';
    }

    public function obtenerEvidenciasPorSolicitud($id_solicitud) {
        return $this->obtenerImagenesPorSolicitud($id_solicitud);
    }
}