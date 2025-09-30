<?php
// Controllers/PerfilProfesionalDao.php
require_once __DIR__ . '/../Model/database.php';

class PerfilProfesionalDao {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
    }

    public function obtenerPerfilProfesional($idUsuario) {
        try {
            // Debug: Verificar que llegue el ID correcto
            error_log("DEBUG: Buscando perfil para usuario ID: " . $idUsuario);
            
            // Primero verificar que el usuario existe
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

            // Verificar si existe como profesional
            $sqlProf = "SELECT COUNT(*) as count FROM profesional WHERE id_Profesional = ?";
            $stmtProf = $this->db->prepare($sqlProf);
            $stmtProf->bind_param("i", $idUsuario);
            $stmtProf->execute();
            $resultProf = $stmtProf->get_result();
            $countProf = $resultProf->fetch_assoc()['count'];
            
            error_log("DEBUG: Usuario existe: $countUsuario, Es profesional: $countProf");

            // Consulta principal mejorada
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
                        u.Calificacion AS calificacion_global,
                        u.verificado,
                        u.Tipo_Usuario,
                        p.id_Profesional,
                        p.experiencia,
                        p.historial,
                        p.especialidad,
                        p.calificaciones AS calificaciones_profesional
                    FROM usuario u
                    LEFT JOIN profesional p ON u.id_Usuario = p.id_Profesional
                    WHERE u.id_Usuario = ? AND u.Tipo_Usuario = 'profesional'";

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
                error_log("DEBUG: Perfil encontrado para: " . $perfil['Nombres']);
                
                // Si no hay registro profesional, completar con valores por defecto
                if (!$perfil['id_Profesional']) {
                    error_log("DEBUG: Usuario no tiene registro en tabla profesional, completando datos...");
                    $perfil['experiencia'] = 'No especificada';
                    $perfil['historial'] = 'Sin historial registrado';
                    $perfil['especialidad'] = 'No especificada';
                    $perfil['calificaciones_profesional'] = '0.0';
                }
            } else {
                error_log("DEBUG: No se encontró perfil para usuario ID: $idUsuario");
            }

            return $perfil;

        } catch (Exception $e) {
            error_log("ERROR en obtenerPerfilProfesional: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Método de debugging para verificar datos
     */
    public function debugUsuario($idUsuario) {
        $sql = "SELECT * FROM usuario WHERE id_Usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        
        error_log("DEBUG USUARIO: " . print_r($usuario, true));
        
        $sqlProf = "SELECT * FROM profesional WHERE id_Profesional = ?";
        $stmtProf = $this->db->prepare($sqlProf);
        $stmtProf->bind_param("i", $idUsuario);
        $stmtProf->execute();
        $resultProf = $stmtProf->get_result();
        $profesional = $resultProf->fetch_assoc();
        
        error_log("DEBUG PROFESIONAL: " . print_r($profesional, true));
        
        return ['usuario' => $usuario, 'profesional' => $profesional];
    }

    public function actualizarPerfilProfesional($idUsuario, $data, $foto = null) {
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
    
            // Actualizar tabla profesional
            $sqlProf = "UPDATE profesional SET 
                            especialidad = ?, 
                            historial = ?
                        WHERE id_Profesional = ?";
            $stmtProf = $this->db->prepare($sqlProf);
            $stmtProf->bind_param(
                "ssi",
                $data['especialidad'],
                $data['historial'],
                $idUsuario
            );
            $stmtProf->execute();
    
            return true;
        } catch (Exception $e) {
            error_log("ERROR actualizarPerfilProfesional: " . $e->getMessage());
            return false;
        }
    }
}

