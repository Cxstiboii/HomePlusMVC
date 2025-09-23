<?php
session_start();

if (!isset($_SESSION["id_Usuario"])) {
    // Si no hay usuario logueado, redirigir
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}

// Id del usuario logueado
$idUsuario = $_SESSION["id_Usuario"];
$tipoUsuario = $_SESSION["tipo_usuario"];

// Si quieres que solo entren profesionales
if ($tipoUsuario !== "profesional") {
    header("Location: /Views/modulo-usuarios/HomePlusFull/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesional.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalServiciosPublicados.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalNav.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalMainContent.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalDetallesServicio.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalPerfil.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalMisTrabajos.css">
    <link rel="icon" href="/Views/assets/Favicon/favicon-96x96.png">
    <title>Sistema de Gestión de Servicios</title>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="logo">
                <img src="/Views/assets/img/Logo/Logo-Home-Transparente.svg" alt="Logo Home+" style="height: 30px;">
                <p><b>Profesional</b></p>
            </div>

            <nav>
                <a href="#" class="nav-item active" data-module="trabajos">
                    <span class="icon">🏠</span>
                    <span>Mis Trabajos</span>
                </a>
                <a href="#" class="nav-item" data-module="servicios">
                    <span class="icon">🛠️</span>
                    <span>Servicios Publicados</span>
                </a>
                <a href="#" class="nav-item" data-module="detalles">
                    <span class="icon">📋</span>
                    <span>Detalles del servicio</span>
                </a>
                
                <a href="#" class="nav-item" data-module="agendamiento">
                    <span class="icon">📅</span>
                    <span>Ver Agendamiento</span>
                </a>
                
                <a href="#" class="nav-item" data-module="perfil-profesional">
                    <span class="icon">👤</span>
                    <span>Perfil Profesional</span>
                </a>
                <!-- Botón de salir -->
                <a href="/Views/modulo-usuarios/HomePlusFull/index.php" class="nav-item nav-logout">
                    <span class="icon">🚪</span>
                    <span>Salir</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Módulo: Mis Trabajos -->
            <div class="module active" id="trabajos">
                <div class="module-header">
                    <h1 class="module-title">Mis Trabajos</h1>
                    <h1 class="module-subtitle">Gestiona todos tus trabajos actualmente </h1>
                </div>

                <!--Instancio el controlador -->
                <?php
                require_once('../../Controllers/TrabajosDao.php');
                $dao = new TrabajosDao();
                
                $idProfesional = $_SESSION['id_Usuario']; // ahora sí existe
                $estado = isset($_GET['estado']) ? $_GET['estado'] : "Todos";
                
                $trabajos = $dao->obtenerTrabajos($idProfesional, $estado);
                ?>


                <!-- Filtros de trabajos -->
                <div class="filtros-trabajos">
                    <!-- Botón visible SOLO en móvil -->
                    <button class="filtro-menu-btn">Filtrar ▾</button>
                
                    <!-- Contenedor de opciones -->
                    <div class="filtros-opciones">
                        <button class="filtro-btn active" data-estado="todos">Todos</button>
                        <button class="filtro-btn" data-estado="Aceptado">Aceptados</button>
                        <button class="filtro-btn" data-estado="Pendiente">Pendientes</button>
                        <button class="filtro-btn" data-estado="Cancelado">Cancelados</button>
                        <button class="filtro-btn" data-estado="Negociado">Negociados</button>
                        <button class="filtro-btn" data-estado="Agendado">Agendados</button>
                    </div>
                </div>

                <!-- Bucle de trabajos -->
                <?php if(count($trabajos) > 0): ?>
                    <?php foreach ($trabajos as $row): ?>
                        <!-- Cada card lleva su estado en un data-atributo -->
                        <div class="card" data-estado="<?php echo htmlspecialchars($row['estado']); ?>">
                            <div class="trabajo-item">
                                <div class="trabajo-info">
                                    <h3><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
                                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['Nombres'] . ' ' . $row['Apellidos']); ?></p>
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/y', strtotime($row['fecha_preferida'])); ?></p>
                                    <p><strong>Urgencia:</strong> <?php echo ucfirst($row['urgencia']); ?></p>
                                </div>
                                <div>
                                    <span class="status-badge status-aceptado"><?php echo htmlspecialchars($row['estado']); ?></span>
                                    <a class="btn-details" href="detalle-trabajo.php?id=<?php echo $row['id_solicitud']; ?>">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-servicios">No tienes trabajos actualmente.</p>
                <?php endif; ?>
            </div>

            <!-- Módulo: Servicios Publicados -->
            <?php
                require_once('../../Controllers/ServiciosPublicadosDao.php');
                $dao = new ServiciosPublicadosDao();
                $servicios = $dao->obtenerServiciosPublicados();
            ?>
            <div class="module" id="servicios">
                <div class="module-header">
                    <h1 class="module-title">Servicios Publicados</h1>
                    <h2 class="module-subtitle">Aquí podrás ver los Servicios Publicados</h2>
                </div>

                <?php if(count($servicios) > 0): ?>
                    <div class="servicios-grid">
                    <?php foreach($servicios as $row): ?>
    <div class="servicio-card">
        <!-- Imagen principal -->
        <?php if (!empty($row['foto_principal'])): ?>
            <img src="<?php echo htmlspecialchars($row['foto_principal']); ?>" alt="Imagen del servicio">
        <?php else: ?>
            <img src="/Views/assets/imagenes-comunes/servicios/electricista.jpg" alt="Imagen por defecto">
        <?php endif; ?>
        
        <!-- Información del servicio -->
        <div class="servicio-info">
            <h3 class="header-servicio"><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
            <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
            <strong class="detalles-subinfo-precio">$55.000</strong>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['Nombres'].' '.$row['Apellidos']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($row['direccion_servicio']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?></p>
            <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_preferida']); ?></p>
            <span class="status-badge"><?php echo htmlspecialchars($row['estado']); ?></span>
        </div>
        
        <!-- Botón Ver Detalles -->
        <div class="servicio-botones">
            <a href="#detalles"
                class="btn-accion"
                onclick="irDetalles(this)"
                data-id-solicitud="<?php echo htmlspecialchars($row['id_solicitud']); ?>"
                data-solicitud-id="<?php echo htmlspecialchars($row['id_solicitud']); ?>"
                data-cliente="<?php echo htmlspecialchars($row['Nombres'].' '.$row['Apellidos']); ?>"
                data-servicio="<?php echo htmlspecialchars($row['titulo_servicio']); ?>"
                data-descripcion="<?php echo htmlspecialchars($row['descripcion']); ?>"
                data-estado="<?php echo htmlspecialchars($row['estado']); ?>"
                data-direccion="<?php echo htmlspecialchars($row['direccion_servicio']); ?>"
                data-fecha="<?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?>"
                data-hora="<?php echo htmlspecialchars($row['hora_preferida']); ?>"
                data-urgencia="<?php echo htmlspecialchars($row['urgencia']); ?>"
                data-precio="55000"
                data-img="<?php echo !empty($row['foto_principal']) ? htmlspecialchars($row['foto_principal']) : '/Views/assets/imagenes-comunes/servicios/electricista.jpg'; ?>">
                Ver Detalles
            </a>
        </div>
    </div>
<?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-servicios">No hay servicios publicados en este momento.</p>
                <?php endif; ?>
            </div>

            <!-- Módulo: Detalles del Servicio -->
            <div class="module" id="detalles" >
                <div class="card module-header">
                    <h1 class="module-title">Detalles del Servicio</h1>
                    <h2 class="module-subtitle">Información completa del servicio seleccionado</h2>
                </div>

                <!-- Sección contenedor -->
                <div class="card detalles-servicio-container">
                    <!-- Columna izquierda: slideshow con imágenes -->
                    <div class="slideshow-wrapper">
                        <div class="slideshow-container">
                            <!-- Imagen dinámica -->
                            <div class="mySlides fade">
                                <div class="numbertext">1 / 1</div>
                                <img id="detalle-img" 
                                        src="/Views/assets/imagenes-comunes/servicios/electricista.jpg" 
                                        style="width:100%; border-radius:20px">
                            </div>

                            <!-- Botones anterior/siguiente -->
                            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                            <a class="next" onclick="plusSlides(1)">&#10095;</a>
                        </div>
                    </div>

                    <!-- Columna derecha: info principal -->
                    <div class="card detalles-info">
                        <!-- Título y descripción -->
                        <div class="contenido-detalles-servicio">
                            <h2 id="servicio-tipo" class="servicio-detalles">Título servicio</h2>
                            <p id="detalle-descripcion" class="detalle-descripcion">
                                Descripción del servicio...
                            </p>
                            <p class="info-detalle">Urgencia: <span id="detalle-urgencia">---</span></p>
                        </div>

                        <!-- Precio + subinfo -->
                        <p><strong>Precio</strong></p>
                        <div class="detalles-subinfo">
                            <p class="detalles-subinfo-precio">
                                <strong id="detalle-precio"></strong>
                            </p>

                            <p><strong>Cliente:</strong> <span id="detalle-cliente"></span></p>
                            <p><strong>Dirección:</strong> <span id="detalle-direccion"></span></p>
                            <p><strong>Fecha programada:</strong> <span id="detalle-fecha"></span></p>
                            <p><strong>Hora:</strong> <span id="detalle-hora"></span></p>
                            <p><strong>Estado:</strong> 
                                <span id="detalle-estado" class="status-badge status-pendiente"></span>
                            </p>

                            <!-- Botones -->
                            <div class="botones-accion">
                                <a href="#" class="status-badge-negociar-agendar status-badge-beige">Ir a Negociar</a>
                                <a href="#" id="agendarBtn" class="status-badge-negociar-agendar status-badge-negro"
                                data-id-solicitud="<?php echo $idSolicitud; ?>">Agendar</a>
                            <!-- Mensaje dinámico -->
                            <p id="agendarMensaje" style="color: green; display: none;">✅ Servicio agendado con éxito</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Modal para ir a negociar -->
                <div id="negotiationModal" class="modal">
                    <div class="modal-content card">
                    <span class="close-btn">&times;</span>

                    <div class="module-negotiation">
                        <h3 class="module-title">🤝 Negociar Oferta</h3>

                        <form action="../../Controllers/ActualizarPerfilProfesionalDao.php" method="POST">
                            <input type="hidden" name="id_solicitud" value="<?php echo htmlspecialchars($idSolicitud); ?>">
                            <input type="hidden" name="id_profesional" value="<?php echo htmlspecialchars($idUsuario); ?>">

                            <!-- Precio -->
                            <div class="input-group">
                                <label for="precio_estimado">💲 Precio Propuesto</label>
                                <input type="number" id="precio_estimado" name="precio_estimado" step="0.01" placeholder="Ej. 150.000 COP" required>
                            </div>

                            <!-- Tiempo -->
                            <div class="input-group">
                                <label for="tiempo_estimado">⏳ Tiempo Estimado (horas)</label>
                                <input type="number" id="tiempo_estimado" name="tiempo_estimado" placeholder="Ej. 3">
                            </div>

                            <!-- Ayudante -->
                            <div class="input-group checkbox-group">
                                <input type="checkbox" id="acompanante" name="acompanante">
                                <label for="acompanante">¿Necesita un ayudante?</label>
                            </div>

                            <!-- Materiales -->
                            <h4 class="sub-title">📦 Desglose de Materiales</h4>
                            <div id="materiales-list">
                                <div class="material-item">
                                    <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)">
                                    <input type="number" name="material_cantidad[]" placeholder="Cantidad">
                                    <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)">
                                </div>
                            </div>
                            <button type="button" class="add-material-btn">➕ Agregar Material</button>

                            <!-- Botón principal -->
                            <button type="submit" class="perfil-boton">🚀 Enviar Oferta</button>
                        </form>
                    </div>
                    </div>
                </div>

                <!-- Modal de Confirmación Mejorado -->
                <div id="confirmModal" class="modal">
                    <div class="modal-content">
                        <span class="close-confirm">&times;</span>
                        <h3>📅 Confirmación de Agendamiento</h3>
                                
                        <!-- Información del servicio seleccionado -->
                        <div class="servicio-info">
                            <h4 id="nombreServicio">Servicio Seleccionado</h4>
                            <p><strong>Descripción:</strong> <span id="descripcionServicio"></span></p>
                            <p><strong>Precio:</strong> <span id="precioServicio" class="precio-destacado"></span></p>
                            <p><strong>Duración:</strong> <span id="duracionServicio"></span></p>
                        </div>
                                
                        <p><strong>¿Desea agendar este servicio?</strong></p>
                                
                        <div class="modal-buttons">
                            <form id="agendarForm" action="../../Controllers/AgendarServicioDao.php" method="POST">
                                <input type="hidden" name="id_solicitud" id="idSolicitudInput">
                                <input type="hidden" name="id_servicio_publicado" id="idServicioPublicadoInput">
                                <input type="hidden" name="nombre_servicio" id="nombreServicioInput">
                                <input type="hidden" name="precio_servicio" id="precioServicioInput">
                                <input type="hidden" name="descripcion_servicio" id="descripcionServicioInput">
                                <button type="submit" class="confirm-btn">✅ Confirmar Agendamiento</button>
                            </form>
                            <button class="cancel-btn" id="cancelarBtn">❌ Cancelar</button>
                        </div>
                    </div>
                </div>

            <?php
            require_once('../../Controllers/PerfilProfesionalDao.php');
                            
            // Inicia sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $idUsuario = $_SESSION['id_Usuario'] ?? null;
            $tipoUsuario = $_SESSION['tipo_usuario'] ?? null;
            
            // Solo profesionales
            if (!$idUsuario || $tipoUsuario !== 'profesional') {
                echo "<p class='no-datos'>Acceso no autorizado. Debes ser un profesional.</p>";
                exit;
            }
            
            $dao = new PerfilProfesionalDao();
            $perfil = $dao->obtenerPerfilProfesional($idUsuario);
            
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
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                    'especialidad' => trim($_POST['especialidad']),
                    'historial' => trim($_POST['historial'])
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
                    $resultado = $dao->actualizarPerfilProfesional($idUsuario, $data, $foto);
                    if ($resultado) {
                        $exito = "Perfil actualizado correctamente ✅";
                        $perfil = $dao->obtenerPerfilProfesional($idUsuario); // recargar datos
                    } else {
                        $error = "Ocurrió un error al actualizar el perfil.";
                    }
                }
            }
            ?>
            
            <div class="module" id="perfil-profesional">
                <div class="card module-header">
                    <h1 class="module-title">👤 Mi Perfil Profesional</h1>
                    <h2 class="module-subtitle">Información de tu cuenta y experiencia</h2>
                </div>
            
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
                
                        <div class="perfil-profesional card">
                            <div class="input-group">
                                <label for="especialidad">💼 Especialidad:</label>
                                <input type="text" id="especialidad" name="especialidad" value="<?= htmlspecialchars($perfil['especialidad']); ?>">
                            </div>
                            <div class="input-group textarea-group">
                                <label for="historial">📂 Historial:</label>
                                <textarea id="historial" name="historial"><?= htmlspecialchars($perfil['historial']); ?></textarea>
                            </div>
                            <div class="input-group disabled-input">
                                <label>⭐ Calificación Global:</label>
                                <input type="text" value="<?= htmlspecialchars($perfil['calificacion_global'] ?? 'N/A'); ?>" disabled>
                            </div>
                            <div class="input-group disabled-input">
                                <label>📊 Calificaciones:</label>
                                <input type="text" value="<?= htmlspecialchars($perfil['calificaciones_profesional'] ?? 'N/A'); ?>" disabled>
                            </div>
                            <div class="input-group disabled-input">
                                <label>✅ Verificado:</label>
                                <input type="text" value="<?= $perfil['verificado'] ? 'Sí' : 'No'; ?>" disabled>
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
                    <p class="no-datos">No se encontró información del profesional.</p>
                <?php endif; ?>
            </div>

            <!-- Módulo: Ver Agendamiento -->
            <div class="module" id="agendamiento">
                <div class="module-header">
                    <h1 class="module-title">Calendario de Agendamientos</h1>
                    <h1 class="module-subtitle">Visualiza todos tus servicios programados</h1>
                </div>
                <div class="card">
                    <h3>Próximos Servicios</h3>
                    
                    <div class="trabajo-item" style="border-color: var(--color-beige);">
                        <div class="trabajo-info">
                            <h3>🔧 Plomería - Reparación de tubería</h3>
                            <p><strong>Cliente:</strong> María González</p>
                            <p><strong>📅 Fecha:</strong> Viernes, 15 de Junio 2025</p>
                            <p><strong>🕐 Hora:</strong> 10:00 AM</p>
                            <p><strong>📍 Dirección:</strong> Calle 45 #12-34, Chapinero</p>
                        </div>
                        <div>
                            <span class="status-badge status-aceptado">Confirmado</span>
                            <button class="btn btn-secondary" onclick="reprogramar()">Reprogramar</button>
                        </div>
                    </div>

                    <div class="trabajo-item" style="border-color: var(--color-beige);">
                        <div class="trabajo-info">
                            <h3>⚡ Electricidad - Instalación de tomas</h3>
                            <p><strong>Cliente:</strong> Carlos Pérez</p>
                            <p><strong>📅 Fecha:</strong> Sábado, 16 de Junio 2025</p>
                            <p><strong>🕐 Hora:</strong> 02:00 PM</p>
                            <p><strong>📍 Dirección:</strong> Carrera 30 #45-67, Zona Rosa</p>
                        </div>
                        <div>
                            <span class="status-badge status-media">Pendiente</span>
                            <button class="btn btn-primary" onclick="confirmarServicio()">Confirmar</button>
                        </div>
                    </div>

                    <div class="trabajo-item" style="border-color: var(--color-beige);">
                        <div class="trabajo-info">
                            <h3>🪚 Carpintería - Reparación de puerta</h3>
                            <p><strong>Cliente:</strong> Ana López</p>
                            <p><strong>📅 Fecha:</strong> Lunes, 18 de Junio 2025</p>
                            <p><strong>🕐 Hora:</strong> 09:00 AM</p>
                            <p><strong>📍 Dirección:</strong> Avenida 68 #23-45, Engativá</p>
                        </div>
                        <div>
                            <span class="status-badge status-aceptado">Confirmado</span>
                            <button class="btn btn-secondary" onclick="verRuta()">Ver Ruta</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal de confirmación para salir -->
    <div class="modal-overlay" id="modalSalir">
        <div class="modal-content">
            <h3 class="modal-title">¿Confirmar salida?</h3>
            <p class="modal-text">¿Estás seguro de que deseas cerrar sesión? Se perderán los datos no guardados.</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button class="modal-btn modal-btn-confirm" onclick="salirSistema()">Salir</button>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script 
    src="/Views/modulo-confirmacion-agendamiento/js/profesional.js"></script>
</body>
</html>