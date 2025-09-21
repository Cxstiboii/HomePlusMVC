<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home+ Cliente</title>
   <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/cliente.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="logo">
                <h2>🏠 Home+ Cliente</h2>
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

    <!-- IMPORTANTE: apunta a tu DAO (controlador) -->
    <form action="/Controllers/nuevoServicioDao.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="crear">

        <div class="card">
            <h3>Selecciona el Tipo de Servicio</h3>
            <div class="service-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Plomería'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">🔧</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Plomería</div>
                </div>
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Electricidad'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">⚡</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Electricidad</div>
                </div>
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Carpintería'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">🪚</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Carpintería</div>
                </div>
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Pintura'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">🎨</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Pintura</div>
                </div>
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Limpieza'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">🧹</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Limpieza</div>
                </div>
                <div class="service-card"
                    style="border:2px solid var(--color-gris);border-radius:12px;padding:20px;cursor:pointer;text-align:center;"
                    onclick="document.getElementById('servicio').value='Jardinería'">
                    <div class="service-icon" style="font-size:40px;margin-bottom:10px;">🌿</div>
                    <div class="service-title" style="font-weight:600;margin-bottom:5px;">Jardinería</div>
                </div>
            </div>
            <!-- Campo oculto que guarda el servicio elegido -->
            <input type="hidden" id="servicio" name="servicio" required>
        </div>

        <div class="card">
            <h3>Detalles de la Solicitud</h3>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Título del Servicio *</label>
                    <input type="text" id="titulo-servicio" name="titulo_servicio" class="form-control" placeholder="Ej: Reparación de fuga en baño" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Urgencia *</label>
                    <select id="urgencia" name="urgencia" class="form-control" required>
                        <option value="">Seleccionar urgencia</option>
                        <option value="alta">Alta - Necesito ayuda inmediata</option>
                        <option value="media">Media - En los próximos días</option>
                        <option value="baja">Baja - Cuando sea conveniente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción del Problema *</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="4" placeholder="Describe detalladamente el problema o servicio que necesitas..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fecha Preferida</label>
                    <input type="date" id="fecha-preferida" name="fecha_preferida" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Hora Preferida</label>
                    <select id="hora-preferida" name="hora_preferida" class="form-control">
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
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Calle 45 #12-34" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Barrio/Localidad *</label>
                    <input type="text" id="barrio" name="barrio" class="form-control" placeholder="Chapinero" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Referencias Adicionales</label>
                <input type="text" id="referencias" name="referencias" class="form-control" placeholder="Edificio azul, portería, apartamento 501">
            </div>

            <div class="form-group">
                <label class="form-label">Subir Fotos del Problema</label>
                <input type="file" id="fotos" name="fotos[]" class="form-control" accept="image/*" multiple>
                <small>Puedes subir varias fotos (JPG, PNG)</small>
            </div>

            <!-- Botón con type="submit" -->
            <button type="submit" class="btn btn-primary">
                ➕ Crear Solicitud
            </button>
        </div>
    </form>
</div>

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

            <!-- Módulo: Perfil -->
            <div class="module" id="perfil">
                <div class="module-header">
                    <h1 class="module-title">Mi Perfil</h1>
                    <p class="module-subtitle">Administra tu información personal</p>
                </div>

                <div class="card profile-card">
                    <div class="profile-avatar">MG</div>
                    <h2>María González</h2>
                    <p>Cliente Premium</p>
                    <p>📍 Bogotá, Colombia</p>
                    <p>⭐ 4.9/5.0 como cliente</p>

                    <div class="form-group" style="margin-top: 30px; text-align: left;">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" value="María González" id="nombre-completo">
                    </div>

                    <div class="form-row" style="text-align: left;">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="maria.gonzalez@email.com" id="email">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" value="(+57) 300 123 4567" id="telefono">
                        </div>
                    </div>

                    <div class="form-group" style="text-align: left;">
                        <label class="form-label">Dirección Principal</label>
                        <input type="text" class="form-control" value="Calle 45 #12-34, Chapinero" id="direccion-principal">
                    </div>

                    <button class="btn btn-primary" onclick="actualizarPerfil()">Actualizar Perfil</button>
                </div>
            </div>

           <!-- Módulo: Historial -->
<div class="module" id="historial">
    <div class="module-header">
        <h1 class="module-title">Historial de Servicios</h1>
        <p class="module-subtitle">Revisa todos tus servicios completados</p>
    </div>

    <div class="card">
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



    <!-- Notificación -->
    <div class="notification" id="notification">
        <p id="notificationMessage"></p>
    </div>

   <script src="/Views/modulo-confirmacion-agendamiento/js/cliente.js"></script>
</body>

</html>