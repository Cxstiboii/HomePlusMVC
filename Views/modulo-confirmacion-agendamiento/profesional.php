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
    <title>Profesional</title>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->

        <!-- Overlay para móviles -->
    <div class="overlay" id="overlay"></div>

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
                <a href="#" class="nav-item" data-module="perfil-profesional">
                    <span class="icon">👤</span>
                    <span>Perfil Profesional</span>
                </a>
                <a href="/Views/modulo-confirmacion-agendamiento/reporteCliente.php" class="nav-item nav-logout">
                    <span class="icon">🚪</span>
                    <span>Reporte de Profeisonal</span>
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
        <!-- Cada card lleva su estado y origen en data-atributos -->
        <div class="card trabajo-card" 
             data-estado="<?php echo htmlspecialchars($row['estado']); ?>"
             data-origen="<?php echo htmlspecialchars($row['origen']); ?>"
             data-urgencia="<?php echo htmlspecialchars($row['urgencia']); ?>">
            
            <div class="trabajo-header">
                <div class="trabajo-titulo">
                    <h3><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
                    <span class="origen-badge origen-<?php echo htmlspecialchars($row['origen']); ?>">
                        <?php echo $row['origen'] === 'oferta' ? 'Por Oferta' : 'Asignado Directo'; ?>
                    </span>
                </div>
                <div class="trabajo-estado">
                    <span class="status-badge status-<?php echo strtolower(htmlspecialchars($row['estado'])); ?>">
                        <?php echo htmlspecialchars($row['estado']); ?>
                    </span>
                </div>
            </div>

            <div class="trabajo-content">
                <!-- Información del cliente -->
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <strong>Cliente:</strong>
                            <span><?php echo htmlspecialchars($row['Nombres'] . ' ' . $row['Apellidos']); ?></span>
                        </div>
                    </div>
                    <?php if(!empty($row['Telefono'])): ?>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Teléfono:</strong>
                            <span><?php echo htmlspecialchars($row['Telefono']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($row['Email'])): ?>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($row['Email']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información de fecha y ubicación -->
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <strong>Fecha preferida:</strong>
                            <span><?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?></span>
                        </div>
                    </div>
                    <?php if(!empty($row['hora_preferida'])): ?>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Hora:</strong>
                            <span><?php echo htmlspecialchars($row['hora_preferida']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($row['direccion_servicio'])): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Dirección:</strong>
                            <span><?php echo htmlspecialchars($row['direccion_servicio']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información de precios y tiempos -->
                <div class="info-section">
                    <?php if($row['origen'] === 'oferta' && !empty($row['precio_estimado'])): ?>
                    <div class="info-item">
                        <i class="fas fa-dollar-sign"></i>
                        <div>
                            <strong>Precio estimado:</strong>
                            <span class="precio">$<?php echo number_format($row['precio_estimado'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    <?php elseif(!empty($row['precio_original'])): ?>
                    <div class="info-item">
                        <i class="fas fa-dollar-sign"></i>
                        <div>
                            <strong>Precio:</strong>
                            <span class="precio">$<?php echo number_format($row['precio_original'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($row['origen'] === 'oferta' && !empty($row['tiempo_estimado'])): ?>
                    <div class="info-item">
                        <i class="fas fa-hourglass-half"></i>
                        <div>
                            <strong>Tiempo estimado:</strong>
                            <span><?php echo htmlspecialchars($row['tiempo_estimado']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información adicional -->
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Urgencia:</strong>
                            <span class="urgencia-<?php echo strtolower(htmlspecialchars($row['urgencia'])); ?>">
                                <?php echo ucfirst($row['urgencia']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if($row['origen'] === 'oferta' && !empty($row['acompanante'])): ?>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Acompañante:</strong>
                            <span><?php echo $row['acompanante'] ? 'Sí' : 'No'; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($row['barrio'])): ?>
                    <div class="info-item">
                        <i class="fas fa-map"></i>
                        <div>
                            <strong>Barrio:</strong>
                            <span><?php echo htmlspecialchars($row['barrio']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Descripción del servicio -->
                <?php if(!empty($row['descripcion'])): ?>
                <div class="info-section descripcion-section">
                    <div class="info-item">
                        <i class="fas fa-file-alt"></i>
                        <div>
                            <strong>Descripción:</strong>
                            <p class="descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Referencias adicionales -->
                <?php if(!empty($row['referencias'])): ?>
                <div class="info-section referencias-section">
                    <div class="info-item">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Referencias:</strong>
                            <p class="referencias"><?php echo htmlspecialchars($row['referencias']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="trabajo-footer">
                <div class="trabajo-metadata">
                    <small>
                        <i class="fas fa-calendar-plus"></i>
                        Solicitud: <?php echo date('d/m/Y', strtotime($row['fecha_solicitud'])); ?>
                    </small>
                    <?php if($row['origen'] === 'oferta' && !empty($row['id_oferta'])): ?>
                    <small>
                        <i class="fas fa-tag"></i>
                        ID Oferta: <?php echo htmlspecialchars($row['id_oferta']); ?>
                    </small>
                    <?php endif; ?>
                </div>
                <div class="trabajo-actions">
                    <?php if(in_array($row['estado'], ['Pendiente', 'Aceptado', 'Negociado'])): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-trabajos">
        <i class="fas fa-briefcase fa-3x"></i>
        <p>No tienes trabajos actualmente.</p>
        <small>Cuando tengas nuevos trabajos, aparecerán aquí.</small>
    </div>
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
                    <img src="<?php echo htmlspecialchars($row['foto_principal']); ?>" 
                         alt="Imagen del servicio" 
                         class="servicio-imagen">
                <?php else: ?>
                    <img src="/Views/assets/imagenes-comunes/servicios/electricista.jpg" 
                         alt="Imagen por defecto" 
                         class="servicio-imagen">
                <?php endif; ?>
                
                <!-- Indicador de múltiples imágenes -->
                <?php if (count($row['imagenes_array']) > 1): ?>
                    <div class="multi-imagen-indicator">
                        <span>+<?php echo count($row['imagenes_array']) - 1; ?> más</span>
                    </div>
                <?php endif; ?>
                
                <!-- Información del servicio -->
                <div class="servicio-info">
                    <h3 class="header-servicio"><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
                    <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <strong class="detalles-subinfo-precio">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></strong>
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
                        onclick="cargarDetallesServicio(this)"
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
                        data-precio="<?php echo htmlspecialchars($row['precio']); ?>"
                        data-imagenes='<?php echo json_encode($row['imagenes_array']); ?>'
                        data-img-principal="<?php echo htmlspecialchars($row['foto_principal']); ?>">
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
<div class="module" id="detalles" style="display: none;">
    <div class="card module-header">
        <h1 class="module-title">Detalles del Servicio</h1>
        <h2 class="module-subtitle">Información completa del servicio seleccionado</h2>
    </div>

    <!-- Sección contenedor -->
    <div class="card detalles-servicio-container">
        <!-- Columna izquierda: slideshow con imágenes -->
        <div class="slideshow-wrapper">
            <div class="slideshow-container">
                <!-- Slides dinámicos se insertarán aquí -->
                <div id="slides-container">
                    <!-- Las imágenes se cargarán dinámicamente con JavaScript -->
                </div>

                <!-- Botones anterior/siguiente -->
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </div>
            
            <!-- Indicadores de puntos -->
            <div id="dots-container" style="text-align:center; margin-top: 20px;">
                <!-- Los puntos se generarán dinámicamente -->
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
                    <button type="button" class="status-badge-negociar-agendar status-badge-beige" 
                    onclick="abrirNegociacionModal()" id="negociarBtn">
                    Ir a Negociar
                    </button>
                    <a href="#" id="agendarBtn" class="status-badge-negociar-agendar status-badge-negro">Agendar</a>
                    <!-- Mensaje dinámico -->
                    <p id="agendarMensaje" style="color: green; display: none;">✅ Servicio agendado con éxito</p>
                </div>
            </div>
        </div>
    </div>
</div>

                    <!-- Modal para Negociar -->
<div id="negotiationModal" class="modal" style="display:none;">
    <div class="modal-content card">
        <span class="close-btn" onclick="cerrarNegociacionModal()">&times;</span>

        <div class="module-negotiation">
            <h3 class="module-title">🤝 Negociar Oferta</h3>

            <form id="negociarForm" method="POST">
                <!-- ID de la solicitud (se rellena automáticamente) -->
                <input type="hidden" name="id_solicitud" id="idSolicitudNegociacion">

                <!-- Precio -->
                <div class="input-group">
                    <label for="precio_estimado">💲 Precio Propuesto (COP)</label>
                    <input type="number" id="precio_estimado" name="precio_estimado" step="0.01" 
                           placeholder="Ej. 150000" required min="1">
                </div>

                <!-- Tiempo -->
                <div class="input-group">
                    <label for="tiempo_estimado">⏳ Tiempo Estimado (horas)</label>
                    <input type="number" id="tiempo_estimado" name="tiempo_estimado" 
                           placeholder="Ej. 3" min="1" max="24">
                </div>

                <!-- Ayudante -->
                <div class="input-group checkbox-group">
                    <input type="checkbox" id="acompanante" name="acompanante" value="1">
                    <label for="acompanante">¿Necesita un ayudante?</label>
                </div>

                <!-- Materiales -->
                <h4 class="sub-title">📦 Desglose de Materiales</h4>
                <div id="materiales-list">
                    <div class="material-item">
                        <input type="text" name="material_tipo[]" placeholder="Tipo (Ej. Tubería PVC)" required>
                        <input type="number" name="material_cantidad[]" placeholder="Cantidad" min="1" required>
                        <input type="text" name="material_unidad[]" placeholder="Unidad (Ej. metros)" required>
                        <button type="button" class="remove-material-btn" onclick="this.parentElement.remove()">❌</button>
                    </div>
                </div>
                <button type="button" class="add-material-btn" onclick="agregarMaterial()">➕ Agregar Material</button>

                <!-- Botón de enviar -->
                <button type="submit" class="negociar-btn">🤝 Enviar Oferta</button>
                
                <!-- Mensajes de respuesta -->
                <div id="negociacionMensaje" style="margin-top: 15px; display: none;"></div>
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


                <!-- Módulo: Mi Perfil Profesional -->
<div class="module" id="perfil-profesional">
    <div class="card module-header">
        <h1 class="module-title">👤 Mi Perfil Profesional</h1>
        <h2 class="module-subtitle">Información de tu cuenta y experiencia</h2>
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
    
            <!-- Columna derecha: Información profesional -->
            <div class="perfil-profesional card">
                <div class="input-group">
                    <label for="especialidad">💼 Especialidad:</label>
                    <input type="text" id="especialidad" name="especialidad" value="<?= htmlspecialchars($perfil['especialidad']); ?>" placeholder="Ej: Plomería, Electricidad, etc.">
                </div>
                <div class="input-group textarea-group">
                    <label for="historial">📂 Experiencia y Historial:</label>
                    <textarea id="historial" name="historial" placeholder="Describe tu experiencia, certificaciones, trabajos destacados..."><?= htmlspecialchars($perfil['historial']); ?></textarea>
                </div>
                <div class="input-group disabled-input">
                    <label>⭐ Calificación Global:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['calificacion_global'] ?? 'N/A'); ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>📊 Calificaciones Recibidas:</label>
                    <input type="text" value="<?= htmlspecialchars($perfil['calificaciones_profesional'] ?? 'N/A'); ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>✅ Estado de Verificación:</label>
                    <input type="text" value="<?= $perfil['verificado'] ? '✅ Verificado' : '⏳ Pendiente'; ?>" disabled>
                </div>
                <div class="input-group disabled-input">
                    <label>👤 Tipo de Usuario:</label>
                    <input type="text" value="Profesional" disabled>
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
    <script src="/Views/modulo-confirmacion-agendamiento/js/profesional.js"></script>
    <script src="/Views/modulo-confirmacion-agendamiento/js/ModalProfesional.js"></script>
</body>
</html>