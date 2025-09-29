<?php
// Controllers/PerfilClienteDao.php
require_once __DIR__ . '/../Model/database.php';

class PerfilClienteDao {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
    }

    public function obtenerPerfilCliente($idUsuario) {
        try {
            error_log("DEBUG: Buscando perfil de cliente para usuario ID: " . $idUsuario);
            
            // Verificar que el usuario existe
            $sqlUsuario = "SELECT COUNT(*) as count FROM usuario WHERE id_Usuario = ?";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bind_param("i", $idUsuario);
            $stmtUsuario->execute();
            $resultUsuario = $stmtUsuario->get_result();
            $countUsuario = $resultUsuario->fetch_assoc()['count'];
            
            if ($countUsuario == 0) {
                error_log("DEBUG: Usuario con ID $idUsuario no existe");
                return null;
            }

            // Consulta principal para cliente
            $sql = "SELECT 
                        u.id_Usuario,
                        u.Foto_Perfil,
                        u.Nombres,
                        u.Apellidos,
                        u.Fecha_Nacimiento,
                        u.Tipo_Documento,
                        u.Numero_Documento,
                        u.Telefono,
                        u.Email,
                        u.Direccion,
                        u.Calificacion,
                        u.verificado,
                        u.Tipo_Usuario,
                        c.id_cliente,
                        c.servicios_solicitados,
                        c.citas_solicitadas,
                        c.calificaciones AS calificaciones_cliente
                    FROM usuario u
                    LEFT JOIN cliente c ON u.id_Usuario = c.id_cliente
                    WHERE u.id_Usuario = ? AND u.Tipo_Usuario = 'cliente'";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("DEBUG: Error al preparar consulta: " . $this->db->error);
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();

            $result = $stmt->get_result();
            $perfil = $result->fetch_assoc();

            if ($perfil) {
                error_log("DEBUG: Perfil de cliente encontrado para: " . $perfil['Nombres']);
                
                // Si no hay registro en cliente, completar con valores por defecto
                if (!$perfil['id_cliente']) {
                    error_log("DEBUG: Usuario no tiene registro en tabla cliente, completando datos...");
                    $perfil['servicios_solicitados'] = '0';
                    $perfil['citas_solicitadas'] = '0';
                    $perfil['calificaciones_cliente'] = '0.0';
                }
            } else {
                error_log("DEBUG: No se encontró perfil de cliente para usuario ID: $idUsuario");
            }

            return $perfil;

        } catch (Exception $e) {
            error_log("ERROR en obtenerPerfilCliente: " . $e->getMessage());
            return null;
        }
    }

    public function actualizarPerfilCliente($idUsuario, $data, $foto = null) {
        try {
            // Actualizar tabla usuario
            $sqlUsuario = "UPDATE usuario SET 
                            Nombres = ?, 
                            Apellidos = ?, 
                            Email = ?, 
                            Telefono = ?, 
                            Direccion = ?, 
                            Fecha_Nacimiento = ?,
                            Foto_Perfil = COALESCE(?, Foto_Perfil)
                            WHERE id_Usuario = ?";
            $stmt = $this->db->prepare($sqlUsuario);
            $stmt->bind_param(
                "sssssssi",
                $data['nombres'],
                $data['apellidos'],
                $data['email'],
                $data['telefono'],
                $data['direccion'],
                $data['fecha_nacimiento'],
                $foto,
                $idUsuario
            );
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            error_log("ERROR actualizarPerfilCliente: " . $e->getMessage());
            return false;
        }
    }
}
?>