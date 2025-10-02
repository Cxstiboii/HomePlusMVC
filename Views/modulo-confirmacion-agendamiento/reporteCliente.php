<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte del Cliente - HomePlus</title>
    <link rel="stylesheet" href="/Views/modulo-confirmacion-agendamiento/css/reporteCliente.css">
    <style>
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 10px 0;
        }
        .rating {
            color: #ffc107;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado del Reporte -->
        <div class="header">
            <h1>📊 Reporte del Cliente</h1>
            <p>Análisis completo de servicios y actividad</p>
        </div>

        <!-- Información del Cliente -->
        <div class="client-info">
            <div class="info-card">
                <h3>👤 Información Personal</h3>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['Nombres'] . ' ' . $cliente['Apellidos']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['Email']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['Telefono']); ?></p>
            </div>
            <div class="info-card">
                <h3>📍 Dirección</h3>
                <p><?php echo htmlspecialchars($cliente['Direccion']); ?></p>
                <p><strong>Documento:</strong> <?php echo htmlspecialchars($cliente['Numero_Documento']); ?></p>
                <p><strong>Estado:</strong> <span class="status-badge status-completed"><?php echo $cliente['verificado'] ? 'Verificado' : 'No Verificado'; ?></span></p>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo isset($stats['total_servicios']) ? $stats['total_servicios'] : 0; ?></div>
                <div class="stat-label">Total Servicios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo isset($stats['servicios_completados']) ? $stats['servicios_completados'] : 0; ?></div>
                <div class="stat-label">Completados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo isset($stats['servicios_agendados']) ? $stats['servicios_agendados'] : 0; ?></div>
                <div class="stat-label">Agendados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo isset($stats['servicios_cancelados']) ? $stats['servicios_cancelados'] : 0; ?></div>
                <div class="stat-label">Cancelados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo isset($stats['calificacion_promedio']) ? number_format($stats['calificacion_promedio'], 1) : '0.0'; ?></div>
                <div class="stat-label">Calificación Promedio</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo isset($stats['inversion_total']) ? number_format($stats['inversion_total'] / 1000000, 2) : '0.00'; ?>M</div>
                <div class="stat-label">Inversión Total</div>
            </div>
        </div>

        <!-- Servicios Completados -->
        <div class="section">
            <div class="section-header">✅ Servicios Completados</div>
            <?php if (!empty($servicios_completados)): ?>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Calificación</th>
                            <th>Profesional</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios_completados as $servicio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($servicio['titulo_servicio']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['tipo_servicio']); ?></td>
                            <td>$<?php echo number_format($servicio['precio'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($servicio['puntuacion']): ?>
                                    <span class="rating">
                                        <?php echo str_repeat('★', $servicio['puntuacion']) . str_repeat('☆', 5 - $servicio['puntuacion']); ?>
                                    </span> (<?php echo $servicio['puntuacion']; ?>/5)
                                <?php else: ?>
                                    Sin calificar
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($servicio['nombre_profesional']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($servicio['fecha_fin'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No hay servicios completados</p>
            <?php endif; ?>
        </div>

        <!-- Servicios Agendados -->
        <div class="section">
            <div class="section-header">📅 Servicios Agendados</div>
            <?php if (!empty($servicios_agendados)): ?>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Tipo</th>
                            <th>Presupuesto</th>
                            <th>Profesional</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios_agendados as $servicio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($servicio['titulo_servicio']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['tipo_servicio']); ?></td>
                            <td>$<?php echo number_format($servicio['precio'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($servicio['nombre_profesional']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($servicio['fecha_asignacion'])); ?></td>
                            <td><?php echo date('H:i', strtotime($servicio['hora_ini'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No hay servicios agendados</p>
            <?php endif; ?>
        </div>

        <!-- Servicios Cancelados -->
        <div class="section">
            <div class="section-header">❌ Servicios Cancelados</div>
            <?php if (!empty($servicios_cancelados)): ?>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Tipo</th>
                            <th>Presupuesto</th>
                            <th>Fecha Solicitud</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios_cancelados as $servicio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($servicio['titulo_servicio']); ?></td>
                            <td><?php echo htmlspecialchars($servicio['tipo_servicio']); ?></td>
                            <td>$<?php echo number_format($servicio['precio'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($servicio['fecha_solicitud'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No hay servicios cancelados</p>
            <?php endif; ?>
        </div>

        <!-- Resumen por Tipo de Servicio -->
        <div class="section">
            <div class="section-header">📋 Resumen por Tipo de Servicio</div>
            <?php if (!empty($resumen_servicios)): ?>
                <div class="summary-grid">
                    <?php foreach ($resumen_servicios as $resumen): 
                        $porcentaje = $max_ingresos > 0 ? ($resumen['total_ingresos'] / $max_ingresos) * 100 : 0;
                    ?>
                    <div>
                        <h4><?php echo htmlspecialchars($resumen['tipo']); ?></h4>
                        <div class="summary-item">
                            <span>Total: <?php echo $resumen['total_servicios']; ?> servicios</span>
                            <span>$<?php echo number_format($resumen['total_ingresos'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No hay datos de resumen</p>
            <?php endif; ?>
        </div>

        <!-- Profesionales Mejor Calificados -->
        <div class="section">
            <div class="section-header">🏆 Profesionales Mejor Calificados</div>
            <?php if (!empty($mejores_profesionales)): ?>
                <?php foreach ($mejores_profesionales as $profesional): ?>
                <div class="professional-card">
                    <div class="professional-header">
                        <span class="professional-name"><?php echo htmlspecialchars($profesional['nombre_profesional']); ?></span>
                        <span class="rating">
                            <?php echo str_repeat('★', round($profesional['calificacion_promedio'])) . str_repeat('☆', 5 - round($profesional['calificacion_promedio'])); ?>
                        </span>
                    </div>
                    <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($profesional['especialidad']); ?></p>
                    <p><strong>Comentario:</strong> "<?php echo htmlspecialchars($profesional['ultimo_comentario']); ?>"</p>
                    <p><strong>Servicios realizados:</strong> <?php echo $profesional['servicios_realizados']; ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No hay profesionales calificados</p>
            <?php endif; ?>
        </div>

        <!-- Botón de Impresión -->
        <div style="text-align: center; margin-top: 30px;">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimir Reporte</button>
        </div>
    </div>

    <script>
        // Animación simple para las tarjetas de estadísticas
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.style.animation = 'fadeInUp 0.6s ease-out';
            });
        });

        // Agregar estilos de animación
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>