// Variables globales
let servicioSeleccionado = '';
let solicitudes = [];

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    configurarNavegacion();
    configurarSeleccionServicios();
    configurarFechaMinima();
    configurarLogout();
    cargarSolicitudesEjemplo();
    actualizarListaSolicitudes();
});

// Configurar navegación
function configurarNavegacion() {
    const navItems = document.querySelectorAll('.nav-item');
    const modules = document.querySelectorAll('.module');

    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Evitar que el logout se comporte como navegación
            if (this.id === 'logout-btn') return;

            navItems.forEach(nav => nav.classList.remove('active'));
            modules.forEach(module => module.classList.remove('active'));

            this.classList.add('active');
            const moduleId = this.getAttribute('data-module');
            document.getElementById(moduleId).classList.add('active');
        });
    });
}

// Configurar logout
function configurarLogout() {
    document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro de que quieres salir?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/modulo-usuarios/HomePlusFull/index.html';
            }
        });
    });
}

// Configurar selección de servicios
function configurarSeleccionServicios() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            serviceCards.forEach(c => {
                c.style.border = '2px solid var(--color-gris)';
                c.style.background = 'var(--color-blanco)';
            });
            
            this.style.border = '2px solid var(--color-beige)';
            this.style.background = 'var(--color-beige-light)';
            
            servicioSeleccionado = this.getAttribute('data-service');
        });
    });
}

// Configurar fecha mínima
function configurarFechaMinima() {
    const fechaInput = document.getElementById('fecha-preferida');
    const today = new Date().toISOString().split('T')[0];
    fechaInput.setAttribute('min', today);
}

// Crear solicitud
function crearSolicitud() {
    const titulo = document.getElementById('titulo-servicio').value;
    const urgencia = document.getElementById('urgencia').value;
    const descripcion = document.getElementById('descripcion').value;
    const fechaPreferida = document.getElementById('fecha-preferida').value;
    const horaPreferida = document.getElementById('hora-preferida').value;
    const direccion = document.getElementById('direccion').value;
    const barrio = document.getElementById('barrio').value;
    const referencias = document.getElementById('referencias').value;

    // Validaciones
    if (!servicioSeleccionado) {
        mostrarNotificacion('Por favor, selecciona un tipo de servicio', 'error');
        return;
    }

    if (!titulo || !urgencia || !descripcion || !direccion || !barrio) {
        mostrarNotificacion('Por favor, completa todos los campos obligatorios', 'error');
        return;
    }

    // Crear nueva solicitud
    const nuevaSolicitud = {
        id: Date.now(),
        servicio: servicioSeleccionado,
        titulo: titulo,
        urgencia: urgencia,
        descripcion: descripcion,
        fechaPreferida: fechaPreferida,
        horaPreferida: horaPreferida,
        direccion: direccion,
        barrio: barrio,
        referencias: referencias,
        estado: 'pendiente',
        fechaCreacion: new Date().toLocaleDateString('es-ES'),
        ofertas: []
    };

    solicitudes.push(nuevaSolicitud);
    limpiarFormulario();

    Swal.fire({
        title: '¡Solicitud creada!',
        text: 'Tu solicitud ha sido creada exitosamente. Los profesionales podrán hacer ofertas pronto.',
        icon: 'success',
        confirmButtonText: 'Ver mis solicitudes'
    }).then(() => {
        cambiarModulo('mis-solicitudes');
        actualizarListaSolicitudes();
    });
}

// Limpiar formulario
function limpiarFormulario() {
    document.getElementById('titulo-servicio').value = '';
    document.getElementById('urgencia').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('fecha-preferida').value = '';
    document.getElementById('hora-preferida').value = '';
    document.getElementById('direccion').value = '';
    document.getElementById('barrio').value = '';
    document.getElementById('referencias').value = '';
    
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.style.border = '2px solid var(--color-gris)';
        card.style.background = 'var(--color-blanco)';
    });
    servicioSeleccionado = '';
}

// Cambiar módulo
function cambiarModulo(moduleId) {
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
    document.querySelectorAll('.module').forEach(module => module.classList.remove('active'));

    document.querySelector(`[data-module="${moduleId}"]`).classList.add('active');
    document.getElementById(moduleId).classList.add('active');
}

// Actualizar lista de solicitudes
function actualizarListaSolicitudes() {
    const container = document.getElementById('solicitudes-container');
    container.innerHTML = '';
    
    if (solicitudes.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--color-gris); padding: 20px;">No tienes solicitudes aún</p>';
        return;
    }

    solicitudes.forEach(solicitud => {
        const solicitudHTML = `
        <div class="trabajo-item" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border: 2px solid var(--color-gris); border-radius: 12px; margin-bottom: 15px;">
            <div class="trabajo-info">
                <h3>${obtenerIconoServicio(solicitud.servicio)} ${solicitud.titulo}</h3>
                <p><strong>Fecha:</strong> ${solicitud.fechaCreacion}</p>
                <p><strong>Urgencia:</strong> ${obtenerBadgeUrgencia(solicitud.urgencia)}</p>
                <p><strong>Estado:</strong> ${obtenerBadgeEstado(solicitud.estado)}</p>
                <p><strong>Dirección:</strong> ${solicitud.direccion}, ${solicitud.barrio}</p>
                <p><strong>Ofertas:</strong> ${solicitud.ofertas.length} profesionales</p>
            </div>
            <div style="text-align: right;">
                <div class="btn-actions">
                    <button class="btn btn-primary" onclick="verOfertas(${solicitud.id})" style="margin: 2px; width: 120px;">Ver Ofertas</button>
                    <button class="btn btn-secondary" onclick="editarSolicitud(${solicitud.id})" style="margin: 2px; width: 120px;">Editar</button>
                    <button class="btn" onclick="eliminarSolicitud(${solicitud.id})" style="margin: 2px; width: 120px; background: #dc3545; color: white;">Eliminar</button>
                </div>
            </div>
        </div>
        `;
        container.innerHTML += solicitudHTML;
    });
}

// Ver ofertas
function verOfertas(solicitudId) {
    const solicitud = solicitudes.find(s => s.id === solicitudId);
    
    let ofertasHTML = `
    <div style="max-height: 400px; overflow-y: auto;">
        <h3 style="margin-bottom: 20px;">Ofertas para: ${solicitud.titulo}</h3>
    `;
    
    if (solicitud.ofertas.length === 0) {
        ofertasHTML += `<p style="text-align: center; color: #666; padding: 20px;">Aún no hay ofertas para esta solicitud</p>`;
    } else {
        solicitud.ofertas.forEach((oferta, index) => {
            ofertasHTML += `
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="margin: 0 0 5px 0;">${oferta.profesional}</h4>
                        <p style="margin: 0; color: #666;">⭐ ${oferta.calificacion}/5.0</p>
                        <p style="margin: 5px 0;"><strong>Precio:</strong> ${formatearMoneda(oferta.precio)}</p>
                        <p style="margin: 0; color: #666;">${oferta.descripcion}</p>
                    </div>
                    <div style="text-align: right;">
                        <button onclick="negociarOferta(${solicitudId}, ${index})" class="btn btn-primary" style="margin: 2px; background: #28a745;">Negociar</button>
                        <button onclick="aceptarOferta(${solicitudId}, ${index})" class="btn btn-primary" style="margin: 2px;">Aceptar</button>
                    </div>
                </div>
            </div>
            `;
        });
    }
    
    ofertasHTML += `</div>`;
    
    Swal.fire({
        title: 'Ofertas Recibidas',
        html: ofertasHTML,
        width: '800px',
        showConfirmButton: false,
        showCloseButton: true
    });
}

// Negociar oferta
function negociarOferta(solicitudId, ofertaIndex) {
    const solicitud = solicitudes.find(s => s.id === solicitudId);
    const oferta = solicitud.ofertas[ofertaIndex];
    
    Swal.fire({
        title: `Negociar con ${oferta.profesional}`,
        html: `
            <div style="text-align: left;">
                <p><strong>Precio actual:</strong> ${formatearMoneda(oferta.precio)}</p>
                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 5px;">Tu propuesta de precio:</label>
                    <input type="number" id="precio-negociado" class="swal2-input" placeholder="Ingresa tu propuesta">
                </div>
                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 5px;">Mensaje para el profesional:</label>
                    <textarea id="mensaje-negociacion" class="swal2-textarea" placeholder="Explica tu propuesta..."></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar Propuesta',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const precio = document.getElementById('precio-negociado').value;
            const mensaje = document.getElementById('mensaje-negociacion').value;
            
            if (!precio || !mensaje) {
                Swal.showValidationMessage('Por favor completa todos los campos');
                return false;
            }
            
            return { precio: precio, mensaje: mensaje };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Propuesta enviada',
                text: `Tu propuesta de ${formatearMoneda(result.value.precio)} ha sido enviada a ${oferta.profesional}`,
                icon: 'success'
            });
        }
    });
}

// Aceptar oferta
function aceptarOferta(solicitudId, ofertaIndex) {
    const solicitud = solicitudes.find(s => s.id === solicitudId);
    const oferta = solicitud.ofertas[ofertaIndex];
    
    Swal.fire({
        title: '¿Aceptar esta oferta?',
        text: `Vas a aceptar la oferta de ${oferta.profesional} por ${formatearMoneda(oferta.precio)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            solicitud.estado = 'aceptado';
            solicitud.profesionalAsignado = oferta.profesional;
            solicitud.precioAceptado = oferta.precio;
            
            actualizarListaSolicitudes();
            
            Swal.fire({
                title: 'Oferta aceptada',
                text: `Has aceptado la oferta de ${oferta.profesional}. Te contactará pronto.`,
                icon: 'success'
            });
        }
    });
}

// Editar solicitud
function editarSolicitud(solicitudId) {
    const solicitud = solicitudes.find(s => s.id === solicitudId);
    cambiarModulo('nueva-solicitud');
    
    // Rellenar formulario
    document.getElementById('titulo-servicio').value = solicitud.titulo;
    document.getElementById('urgencia').value = solicitud.urgencia;
    document.getElementById('descripcion').value = solicitud.descripcion;
    document.getElementById('fecha-preferida').value = solicitud.fechaPreferida;
    document.getElementById('hora-preferida').value = solicitud.horaPreferida;
    document.getElementById('direccion').value = solicitud.direccion;
    document.getElementById('barrio').value = solicitud.barrio;
    document.getElementById('referencias').value = solicitud.referencias;
    
    // Seleccionar servicio
    const serviceCard = document.querySelector(`[data-service="${solicitud.servicio}"]`);
    if (serviceCard) {
        serviceCard.style.border = '2px solid var(--color-beige)';
        serviceCard.style.background = 'var(--color-beige-light)';
        servicioSeleccionado = solicitud.servicio;
    }
}

// Eliminar solicitud
function eliminarSolicitud(solicitudId) {
    Swal.fire({
        title: '¿Eliminar solicitud?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            solicitudes = solicitudes.filter(s => s.id !== solicitudId);
            actualizarListaSolicitudes();
            
            Swal.fire({
                title: 'Solicitud eliminada',
                text: 'La solicitud ha sido eliminada exitosamente',
                icon: 'success'
            });
        }
    });
}

// Actualizar perfil
function actualizarPerfil() {
    const nombre = document.getElementById('nombre-completo').value;
    const email = document.getElementById('email').value;
    const telefono = document.getElementById('telefono').value;
    const direccion = document.getElementById('direccion-principal').value;

    if (!nombre || !email || !telefono || !direccion) {
        mostrarNotificacion('Por favor, completa todos los campos', 'error');
        return;
    }

    Swal.fire({
        title: 'Perfil actualizado',
        text: 'Tus datos han sido actualizados correctamente',
        icon: 'success'
    });
}

// Cargar solicitudes de ejemplo
function cargarSolicitudesEjemplo() {
    const solicitudEjemplo = {
        id: 1,
        servicio: 'plomeria',
        titulo: 'Reparación de fuga en cocina',
        urgencia: 'alta',
        descripcion: 'Tengo una fuga en la tubería debajo del lavaplatos de la cocina',
        fechaPreferida: '2025-07-08',
        horaPreferida: '10:00',
        direccion: 'Calle 72 #15-23',
        barrio: 'Zona Rosa',
        referencias: 'Edificio Torre Verde, Apartamento 804',
        estado: 'pendiente',
        fechaCreacion: '03/07/2025',
        ofertas: [
            {
                profesional: 'Carlos Mendoza',
                calificacion: 4.8,
                precio: 120000,
                descripcion: 'Experto en reparaciones de plomería. Incluye materiales y mano de obra.'
            },
            {
                profesional: 'Ana Rodríguez',
                calificacion: 4.9,
                precio: 110000,
                descripcion: 'Plomera certificada con 10 años de experiencia. Garantía de 6 meses.'
            }
        ]
    };

    solicitudes.push(solicitudEjemplo);
}

// Funciones auxiliares
function obtenerIconoServicio(servicio) {
    const iconos = {
        'plomeria': '🔧',
        'electricidad': '⚡',
        'carpinteria': '🪚',
        'pintura': '🎨',
        'limpieza': '🧹',
        'jardineria': '🌿'
    };
    return iconos[servicio] || '⚙️';
}

function obtenerBadgeUrgencia(urgencia) {
    const badges = {
        'alta': '<span style="background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Alta</span>',
        'media': '<span style="background: #fff3e0; color: #f57c00; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Media</span>',
        'baja': '<span style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Baja</span>'
    };
    return badges[urgencia] || urgencia;
}

function obtenerBadgeEstado(estado) {
    const badges = {
        'pendiente': '<span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Pendiente</span>',
        'aceptado': '<span style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Aceptado</span>',
        'completado': '<span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Completado</span>'
    };
    return badges[estado] || estado;
}

function formatearMoneda(cantidad) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(cantidad);
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');
    
    notificationMessage.textContent = mensaje;
    notification.className = `notification ${tipo} show`;
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}