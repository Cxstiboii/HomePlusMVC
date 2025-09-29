<?php
session_start();

if (!isset($_SESSION["id_Usuario"])) {
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}

$idUsuario = $_SESSION["id_Usuario"];
$tipoUsuario = $_SESSION["tipo_usuario"];

if ($tipoUsuario !== "cliente") {
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home+ Cliente</title>
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/cliente.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/clientePerfil.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/clienteNav.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-content">
                    <h2>🏠 Home+ Cliente</h2>
                </div>
            </div>

            <nav>
                <a href="#" class="nav-item active" data-module="nueva-solicitud">
                    <span class="icon">➕</span>
                    <span>Nueva Solicitud</span>
                </a>

                <a href="#" class="nav-item" data-module="mis-solicitudes">
                    <span class="icon">📋</span>
                    <span>Mis Solicitudes</span>
                </a>

                <a href="#" class="nav-item" data-module="perfil">
                    <span class="icon">👤</span>
                    <span>Mi Perfil</span>
                </a>

                <a href="#" class="nav-item" data-module="historial">
                    <span class="icon">📊</span>
                    <span>Historial</span>
                </a>

                <a href="#" class="nav-item" id="logout-btn">
                    <span class="icon">🚪</span>
                    <span>Salir</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Módulo: Nueva Solicitud -->
            <div class="module active" id="nueva-solicitud">
                <div class="module-header">
                    <h1 class="module-title">Nueva Solicitud de Servicio</h1>
                    <p class="module-subtitle">Solicita un servicio profesional para tu hogar</p>
                </div>

                <div class="card">
                    <h3>Selecciona el Tipo de Servicio</h3>
                    <div class="service-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                        <div class="service-card" data-service="plomeria" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">🔧</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Plomería</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Reparaciones, instalaciones, fugas</div>
                        </div>
                        <div class="service-card" data-service="electricidad" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">⚡</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Electricidad</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Instalaciones eléctricas, reparaciones</div>
                        </div>
                        <div class="service-card" data-service="carpinteria" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">🪚</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Carpintería</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Muebles, puertas, ventanas</div>
                        </div>
                        <div class="service-card" data-service="pintura" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">🎨</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Pintura</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Pintado interior y exterior</div>
                        </div>
                        <div class="service-card" data-service="limpieza" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">🧹</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Limpieza</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Limpieza profunda, mantenimiento</div>
                        </div>
                        <div class="service-card" data-service="jardineria" style="border: 2px solid var(--color-gris); border-radius: 12px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                            <div class="service-icon" style="font-size: 40px; margin-bottom: 10px;">🌿</div>
                            <div class="service-title" style="font-weight: 600; margin-bottom: 5px;">Jardinería</div>
                            <div class="service-desc" style="color: var(--color-gris); font-size: 14px;">Mantenimiento de jardines</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Detalles de la Solicitud</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Título del Servicio *</label>
                            <input type="text" class="form-control" id="titulo-servicio" placeholder="Ej: Reparación de fuga en baño" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Urgencia *</label>
                            <select class="form-control" id="urgencia" required>
                                <option value="">Seleccionar urgencia</option>
                                <option value="alta">Alta - Necesito ayuda inmediata</option>
                                <option value="media">Media - En los próximos días</option>
                                <option value="baja">Baja - Cuando sea conveniente</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción del Problema *</label>
                        <textarea class="form-control" rows="4" id="descripcion" placeholder="Describe detalladamente el problema o servicio que necesitas..." required></textarea>
                    </div>

                    <div>
                        <div class="form-group">
                            <label class="form-label">Precio</label>
                            <input
                                type="number"
                                class="form-control"
                                id="precio"
                                name="precio"
                                placeholder="Precio dispuesto"
                                step="0.01"
                                min="0"
                                required>
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Fecha Preferida</label>
                            <input type="date" class="form-control" id="fecha-preferida">
                        </div>


                        <div class="form-group">
                            <label class="form-label">Hora Preferida</label>
                            <select class="form-control" id="hora-preferida">
                                <option value="">Flexible</option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                                <option value="17:00">05:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <h4 style="margin: 25px 0 15px 0;">Dirección del Servicio</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Dirección *</label>
                            <input type="text" class="form-control" id="direccion" placeholder="Calle 45 #12-34" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Barrio/Localidad *</label>
                            <input type="text" class="form-control" id="barrio" placeholder="Chapinero" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Referencias Adicionales</label>
                        <input type="text" class="form-control" id="referencias" placeholder="Edificio azul, portería, apartamento 501">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subir Fotos del Problema</label>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" accept="image/*" multiple>
                        <small>Puedes subir varias fotos (JPG, PNG)</small>
                    </div>

                    <button class="btn btn-primary" onclick="crearSolicitud()">
                        ➕ Crear Solicitud
                    </button>
                </div>
            </div> <!-- CIERRA nueva-solicitud -->

            <!-- Módulo: Mis Solicitudes -->
            <div class="module" id="mis-solicitudes">
                <div class="module-header">
                    <h1 class="module-title">Mis Solicitudes</h1>
                    <p class="module-subtitle">Administra todas tus solicitudes de servicio</p>
                </div>

                <div class="card">
                    <h3>Solicitudes Activas</h3>
                    <div id="solicitudes-container">
                        <!-- Las solicitudes se cargarán dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- Módulo: Mi Perfil -->
<div class="module" id="perfil">
    <div class="card module-header">
        <h1 class="module-title">👤 Mi Perfil</h1>
        <h2 class="module-subtitle">Información de tu cuenta</h2>
    </div>

    <?php
    require_once('../../Controllers/PerfilClienteDao.php');
    
    // Inicia sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $idUsuario = $_SESSION['id_Usuario'] ?? null;
    $tipoUsuario = $_SESSION['tipo_usuario'] ?? null;
    
    // Solo clientes
    if (!$idUsuario || $tipoUsuario !== 'cliente') {
        echo "<p class='no-datos'>Acceso no autorizado. Debes ser un cliente.</p>";
        exit;
    }
    
    $dao = new PerfilClienteDao();
    $perfil = $dao->obtenerPerfilCliente($idUsuario);
    
    // Variables para mensajes
    $error = '';
    $exito = '';
    
    // Procesar el formulario si se envía
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'email' => trim($_POST['email']),
            'telefono' => trim($_POST['telefono']),
            'direccion' => trim($_POST['direccion']),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null
        ];
    
        $foto = null;
    
        // Subida de foto
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['foto_perfil']['tmp_name'];
            $fileName = basename($_FILES['foto_perfil']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
        
            if (in_array($fileExt, $allowed)) {
                $newFileName = 'perfil_' . $idUsuario . '.' . $fileExt;
                $uploadDir = 'C:/Users/Samuel/Desktop/HomePlusMVC/Views/assets/uploads/img-usuarios/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $uploadPath = $uploadDir . $newFileName;
            
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $foto = '/Views/assets/uploads/img-usuarios/' . $newFileName;
                } else {
                    $error = "No se pudo subir la imagen.";
                }
            } else {
                $error = "Formato de imagen no permitido. Solo jpg, jpeg, png o gif.";
            }
        }
    
        // Actualizar perfil solo si no hubo error en la imagen
        if (!$error) {
            $resultado = $dao->actualizarPerfilCliente($idUsuario, $data, $foto);
            if ($resultado) {
                $exito = "Perfil actualizado correctamente ✅";
                $perfil = $dao->obtenerPerfilCliente($idUsuario); // recargar datos
            } else {
                $error = "Ocurrió un error al actualizar el perfil.";
            }
        }
    }
    ?>

    <?php if ($perfil): ?>
    <form class="perfil-container card" method="POST" enctype="multipart/form-data">
        <div class="perfil-foto">
            <label for="foto-perfil-input">
                <img src="<?= htmlspecialchars($perfil['Foto_Perfil'] ?? '/Views/assets/imagenes-comunes/user-default.png'); ?>" 
                            alt="Foto de perfil" class="perfil-img" id="foto-preview">
                <span class="edit-icon">✏️</span>
            </label>
            <input type="file" name="foto_perfil" id="foto-perfil-input" accept="image/*" style="display: none;">
        </div>
    
        <div class="perfil-info-wrapper">
            <div class="perfil-info">
                <div class="input-group">
                    <label for="nombres">Nombres:</label>
                    <input type="text" id="nombres" name="nombres" value="<?= htmlspecialchars($perfil['Nombres']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($perfil['Apellidos']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">📧 Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($perfil['Email']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="telefono">📱 Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($perfil['Telefono']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="direccion">📍 Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($perfil['Direccion']); ?>" required>
                </div>
                <div class="input-group disabled-input">
                    <label>🪪 Documento:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['Tipo_Documento'] . ' ' . $perfil['Numero_Documento']); ?>" disabled>
                </div>
                <div class="input-group">
                    <label for="fecha_nacimiento">🎂 Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($perfil['Fecha_Nacimiento']); ?>">
                </div>
            </div>
    
            <!-- Columna derecha: Estadísticas del cliente (similar al profesional) -->
            <div class="perfil-cliente card">
                <div class="input-group disabled-input">
                    <label>📊 Servicios Solicitados:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['servicios_solicitados'] ?? '0'); ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>📅 Citas Solicitadas:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['citas_solicitadas'] ?? '0'); ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>⭐ Calificación Global:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['Calificacion'] ?? 'N/A'); ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>✅ Verificado:</label>
                    <input type="text" value="<?= $perfil['verificado'] ? 'Sí' : 'No'; ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>👤 Tipo de Usuario:</label>
                    <input type="text" value="Cliente" disabled>
                </div>
            </div>
        </div>
    
        <button type="submit" class="perfil-boton">Guardar Cambios</button>
    
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($exito): ?>
            <p class="exito"><?= htmlspecialchars($exito); ?></p>
        <?php endif; ?>
    </form>
    <?php else: ?>
        <p class="no-datos">No se encontró información del perfil.</p>
    <?php endif; ?>
</div>

            <!-- Módulo: Historial -->
            <div class="module" id="historial">
                <div class="module-header">
                    <h1 class="module-title">Historial de Servicios</h1>
                    <p class="module-subtitle">Revisa todos tus servicios completados</p>
                </div>

                <div class="card">
                    <h3>Servicios Completados</h3>
                    <?php if (empty($servicios)): ?>
                    <p>No hay servicios finalizados en el sistema.</p>
                    <?php else: ?>
                    <?php foreach ($servicios as $s): ?>
                    <div class="trabajo-item">
                        <div class="trabajo-info">
                            <h3>🔧 <?php echo htmlspecialchars($s['titulo_servicio']); ?></h3>
                            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($s['direccion_servicio']); ?></p>
                            <p><strong>Fecha Inicio:</strong> <?php echo htmlspecialchars($s['fecha_ini']); ?></p>
                            <p><strong>Fecha Fin:</strong> <?php echo htmlspecialchars($s['fecha_fin']); ?></p>
                            <p><strong>Costo:</strong> $<?php echo htmlspecialchars($s['precio']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="status-badge"> <?php echo htmlspecialchars($s['estado']); ?> </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Notificación -->
            <div class="notification" id="notification">
                <p id="notificationMessage"></p>
            </div>
        </div>
    </div>

    <script src="/Views/modulo-confirmacion-agendamiento/js/cliente.js"></script>
    <script>
        let servicioSeleccionado = "";

        // Detectar servicio elegido
        document.querySelectorAll(".service-card").forEach(card => {
            card.addEventListener("click", () => {
                servicioSeleccionado = card.getAttribute("data-service");
                document.querySelectorAll(".service-card").forEach(c => c.style.border = "2px solid #ccc");
                card.style.border = "2px solid blue";
            });
        });

        function crearSolicitud() {
            if (!servicioSeleccionado) {
                Swal.fire("⚠️ Atención", "Debes seleccionar un tipo de servicio", "warning");
                return;
            }

            const formData = new FormData();
            formData.append("titulo", document.getElementById("titulo-servicio").value);
            formData.append("urgencia", document.getElementById("urgencia").value);
            formData.append("descripcion", document.getElementById("descripcion").value);
            formData.append("fecha_preferida", document.getElementById("fecha-preferida").value);
            formData.append("hora_preferida", document.getElementById("hora-preferida").value);
            formData.append("direccion", document.getElementById("direccion").value);
            formData.append("barrio", document.getElementById("barrio").value);
            formData.append("referencias", document.getElementById("referencias").value);
            formData.append("servicio", servicioSeleccionado);
            formData.append("precio", document.getElementById("precio").value);

            // Subir fotos
            let fotos = document.getElementById("fotos").files;
            for (let i = 0; i < fotos.length; i++) {
                formData.append("fotos[]", fotos[i]);
            }

            fetch("../../Controllers/nuevoServicioDao.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("✅ Éxito", "Solicitud creada correctamente", "success");
                    } else {
                        Swal.fire("❌ Error", data.message, "error");
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    Swal.fire("⚠️ Error", "Ocurrió un problema al enviar la solicitud", "error");
                });
        }
    </script>
</body>
</html>