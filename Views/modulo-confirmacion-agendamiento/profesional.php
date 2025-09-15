<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesional.css">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesionalServiciosPublicados.css">

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
                    <span>Trabajos Aceptados</span>
                </a>
                <a href="#" class="nav-item" data-module="servicios">
                    <span class="icon">🛠️</span>
                    <span>Servicios Publicados</span>
                </a>
                <a href="#" class="nav-item" data-module="perfil">
                    <span class="icon">👤</span>
                    <span>Perfil Profesional</span>
                </a>
                <a href="#" class="nav-item" data-module="detalles">
                    <span class="icon">📋</span>
                    <span>Detalles de la Oferta</span>
                </a>
                <a href="#" class="nav-item" data-module="agendamiento">
                    <span class="icon">📅</span>
                    <span>Ver Agendamiento</span>
                </a>
                <!-- Botón de salir -->
                <a href="#" class="nav-item nav-logout" onclick="confirmarSalida(event)">
                    <span class="icon">🚪</span>
                    <span>Salir</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Módulo: Trabajos Aceptados -->
            <div class="module active" id="trabajos">
                <div class="module-header">
                    <h1 class="module-title">Trabajos Aceptados</h1>
                    <h1 class="module-subtitle">Gestiona todos tus trabajos actualmente aceptados</h1>
                </div>

                <!--Instancio el controlador -->
                <?php
                require_once('../../Controllers/TrabajosAceptadosDao.php');
                $dao = new TrabajosAceptadosDao();
                $trabajos = $dao->obtenerTrabajosAceptados();
                ?>

                <!--Hago un bucle para que haga las cartas en base de lo que hay en mysql y la consulta-->
                <?php if(count($trabajos) > 0):?>
                    <?php foreach ($trabajos as $row): ?>

                <div class="card">
                    <div class="trabajo-item">
                        <div class="trabajo-info">
                            <h3><?php echo htmlspecialchars ($row ['titulo_servicio']); ?></h3>
                            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row ['Nombres'] . ' ' . $row['Apellidos']);?></p>
                            <p><strong>Fecha:</strong> <?php echo date ('d/m/y', strtotime($row['fecha_preferida']));?></p>
                            <p><strong>Urgencia:</strong> <?php echo ucfirst ($row ['urgencia']);?></p>
                        </div>
                        <div>
                            <span class="status-badge status-aceptado"><?php echo htmlspecialchars($row ['estado']);?></span>
                            <a class="btn-details" href="detalle-trabajo.php?id=<?php echo $row['id_solicitud']; ?>">Ver Detalles</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                    <?php else: ?>
                <p class = "no-servicios">No tienes trabajos aceptados actualmente.</p>
                <?php endif; ?>
            </div>

            <<!-- Módulo: Servicios Publicados -->
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
                                alt="Imagen del servicio">
                    <?php else: ?>
                        <img src="/Views/assets/img/default-service.jpg" 
                                alt="Imagen por defecto">
                    <?php endif; ?>

                    <!-- Información del servicio -->
                    <div class="servicio-info">
                        <h3 class="header-servicio"><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['Nombres'].' '.$row['Apellidos']); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($row['direccion_servicio']); ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?></p>
                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_preferida']); ?></p>
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <span class="status-badge"><?php echo htmlspecialchars($row['estado']); ?></span>
                    </div>

                    <!-- Botones -->
                    <div class="servicio-botones">
                        <a href="#"
                            class="btn-accion"
                            onclick="irDetalles(this)"
                            data-cliente="<?php echo htmlspecialchars($row['Nombres'].' '.$row['Apellidos']); ?>"
                            data-servicio="<?php echo htmlspecialchars($row['titulo_servicio']); ?>"
                            data-estado="<?php echo htmlspecialchars($row['estado']); ?>"
                            data-direccion="<?php echo htmlspecialchars($row['direccion_servicio']); ?>"
                            data-fecha="<?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?>"
                            data-hora="<?php echo htmlspecialchars($row['hora_preferida']); ?>"
                            data-urgencia="<?php echo htmlspecialchars($row['urgencia']); ?>">
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
            <div class="module" id="detalles">
                <div class="card module-header">
                    <h1 class="module-title">Detalles del Servicio</h1>
                    <h1 class="module-subtitle">Información completa del servicio seleccionado</h1>
                </div>

                <div class="card detalles-card">
                    <div class="detalles-container">
                        <!-- Columna izquierda: info principal -->
                        <div class=" card detalles-info">

                            <!--Titulo y contenido -->
                            <div class="contenido-detalles-servicio">
                                <!-- Título principal -->
                                <h2 class="servicio-detalles" id="servicio-tipo">
                                    Reparación de lavamanos
                                </h2>

                                <!-- Subinfo: urgencia y estado -->
                                <div class="detalles-subinfo">
                                <p class="info-detalle">Urgencia: <span>Alta</span></p>
                                </div>
                            </div>
                                
                            <p ><strong>Cliente:</strong> <span id="cliente-nombre">Samuel David Castillo Cuellar</span></p>
                            <p> <strong>Descripción:</strong></p>
                            <p id="detalle-descripcion">
                                Se rompió la válvula del lavamanos y está goteando.
                            </p>
                            
                            
                            <p ><strong>Estado:</strong> 
                                <span id="detalle-estado" class="status-badge status-pendiente">Pendiente</span>
                            </p>
                            <p ><strong>Fecha de la Solicitud:</strong> <span id="detalle-fecha">2025-09-18</span></p>
                            <p "><strong>Hora de la Solicitud:</strong> <span id="detalle-hora">09:30 AM</span></p>
                            
                        </div>

                        <!-- Columna derecha: imágenes -->
                        <div class=" detalles-imagenes">
                            <div class="imagenes-grid">
                                <img id="detalle-imagen-1" src="/Views/assets/uploads/service-requests/foto1.jpg" alt="Imagen 1">
                                <img id="detalle-imagen-2" src="/Views/assets/uploads/service-requests/foto2.jpg" alt="Imagen 2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Módulo: Perfil Profesional -->
            <div class="module" id="perfil">
                <div class="module-header">
                    <h1 class="module-title">Perfil Profesional</h1>
                    <h1 class="module-subtitle">Administra tu información profesional y estadísticas</h1>
                </div>
                <div class="card profile-card">
                    <div class="profile-avatar">JP</div>
                    <h2>Juan Pablo Martínez</h2>
                    <p>Técnico Especializado en Plomería y Electricidad</p>
                    <p>📍 Bogotá, Colombia</p>
                    <p>⭐ 4.8/5.0 (127 reseñas)</p>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">156</div>
                            <div class="stat-label">Trabajos Completados</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfacción Cliente</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">3</div>
                            <div class="stat-label">Años Experiencia</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">24h</div>
                            <div class="stat-label">Tiempo Respuesta</div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary" style="margin-top: 20px;">Editar Perfil</button>
                </div>
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

    <!-- Notificación flotante -->
    <div class="notification" id="notification">
        <span id="notificationMessage">Operación realizada con éxito</span>
    </div>

    <!-- Scripts -->
    <script src="/Views/modulo-confirmacion-agendamiento/js/profesional.js"></script>
</body>
</html>