<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/profesional.css">
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

            <!-- Módulo: Servicios Publicados -->
            <?php
                require_once('../../Controllers/ServiciosPublicadosDao.php');
                $dao = new ServiciosPublicadosDao();
                $servicios = $dao -> obtenerServiciosPublicados();
            ?>
            <div class="module" id="servicios">
                <div class="module-header">
                    <h1 class="module-title">Servicios Publicados</h1>
                    <h1 class="module-subtitle">Aquí podrás ver los Servicios Publicados</h1>
                </div>
                                
                <?php if(count($servicios) > 0): ?>
                    <div class="card servicios-grid">
                        <?php foreach($servicios as $row ): ?>
                            <div class="servicio-card">
                                
                                <!-- Imagen principal -->
                                <?php if (!empty($row['foto_principal'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['foto_principal']); ?>" 
                                            alt="Imagen del servicio" class="servicio-img">
                                <?php else: ?>
                                    <img src="/Views/assets/img/default-service.jpg" 
                                            alt="Imagen por defecto" class="servicio-img">
                                <?php endif; ?>
                                
                                <h3><?php echo htmlspecialchars($row['titulo_servicio']); ?></h3>
                                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['Nombres'].' '.$row['Apellidos']); ?></p>
                                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($row['direccion_servicio']); ?></p>
                                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($row['fecha_preferida'])); ?></p>
                                <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_preferida']); ?></p>
                                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($row['descripcion']); ?></p>
                                <span class="status-badge"><?php echo htmlspecialchars($row['estado']); ?></span>
                                <a class="btn-details" href="detalle-servicio.php?id=<?php echo $row['id_solicitud']; ?>">Ver Detalles</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-servicios">No hay servicios publicados en este momento.</p>
                <?php endif; ?>
            </div>



            <!-- Overlay Detalle (Modal) -->
            <div id="detalleOverlay" class="detalle-overlay" style="display:none;">
                <div class="detalle-content">
                    <h2 id="detalleTitulo">Servicio</h2>
                    <p id="detalleDescripcion">Descripción</p>

                    <div class="detalle-buttons">
                        <div class="detalle-row">
                            <button onclick="redirigirANegociacion()">Ir a Negociación</button>
                            <button onclick="redirigirAConfirmacion()">Ir a Confirmación</button>
                        </div>
                        <div class="detalle-row">
                            <button class="btn-salir" onclick="cerrarDetalle()">Salir</button>
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

            <!-- Módulo: Detalles de la Oferta -->
            <div class="module" id="detalles">
                <div class="module-header">
                    <h1 class="module-title">Detalles del Trabajo</h1>
                    <h1 class="module-subtitle">Información completa del trabajo seleccionado</h1>
                </div>
                <div class="card">
                    <div class="form-row">
                        <div>
                            <p><strong>Cliente:</strong> <span id="cliente-nombre">María González</span></p>
                            <p><strong>Urgencia:</strong> <span class="status-badge status-media">Media</span></p>
                        </div>
                        <div>
                            <p><strong>Servicio:</strong> <span id="servicio-tipo">Plomería - Reparación de tubería</span></p>
                            <p><strong>Estado:</strong> <span class="status-badge status-aceptado">Aceptado</span></p>
                        </div>
                    </div>

                    <h3 style="margin: 25px 0 15px 0;">Confirmar Agendamiento</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Fecha del Servicio *</label>
                            <input type="date" class="form-control" id="fecha-servicio" required>
                            <small style="color: var(--color-beige); margin-top: 5px; display: block;">Selecciona una fecha disponible</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Hora del Servicio *</label>
                            <select class="form-control" id="hora-servicio" required>
                                <option value="">Seleccionar hora</option>
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

                    <div class="form-group">
                        <label class="form-label">Notas adicionales</label>
                        <textarea class="form-control" rows="4" placeholder="Instrucciones especiales, materiales necesarios, etc."></textarea>
                    </div>

                    <button class="btn btn-primary" onclick="confirmarAgendamiento()">
                        📅 Confirmar Agendamiento
                    </button>

                    <h3 style="margin: 35px 0 15px 0;">Ubicación del Servicio</h3>
                    <div class="ubicacion-info">
                        <p><strong>Dirección:</strong> Calle 45 #12-34, Chapinero</p>
                        <p><strong>Ciudad:</strong> Bogotá, Colombia</p>
                        <p><strong>Referencia:</strong> Edificio azul, apartamento 501</p>
                    </div>
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