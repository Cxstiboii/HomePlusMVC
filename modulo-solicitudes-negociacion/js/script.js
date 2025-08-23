document.addEventListener('DOMContentLoaded', function() {
    // 1. Configurar fecha mínima (a partir de mañana)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    document.getElementById('serviceDate').min = tomorrow.toISOString().split('T')[0];

    // 2. Manejar el formulario de contratación
    const contractForm = document.getElementById('finalizeContract');
    if (contractForm) {
        contractForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const serviceDate = formatDate(formData.get('serviceDate'));
            const serviceTime = formData.get('serviceTime');
            const paymentMethod = getPaymentMethodName(formData.get('paymentMethod'));
            
            // Mostrar confirmación
            alert(`¡Contratación confirmada con María González!\n\nFecha: ${serviceDate}\nHora: ${serviceTime}\nMétodo de pago: ${paymentMethod}`);
            
            // Redirigir después de 1 segundo
            setTimeout(() => {
                window.location.href = '/modulo-confirmacion-agendamiento/profesional.html';
            }, 1000);
        });
    }

    // 3. Función para formatear fecha
    function formatDate(dateString) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    // 4. Función para nombre del método de pago
    function getPaymentMethodName(value) {
        const methods = {
            'creditCard': 'Tarjeta de crédito',
            'debitCard': 'Tarjeta débito',
            'pse': 'PSE'
        };
        return methods[value] || value;
    }

    // 5. Funcionalidad del chat
    const chatInput = document.querySelector('.chat-input input');
    const sendBtn = document.querySelector('.btn-send');
    
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        const chatContainer = document.querySelector('.chat-messages');
        const timeString = new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});

        // Mensaje del usuario
        chatContainer.innerHTML += `
            <div class="message client">
                <div class="message-content">
                    <p>${message}</p>
                    <span class="message-time">${timeString}</span>
                </div>
            </div>
        `;
        
        chatInput.value = '';
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Respuesta automática
        setTimeout(() => {
            chatContainer.innerHTML += `
                <div class="message professional">
                    <div class="message-content">
                        <p>¡Entendido! Quedo atenta a cualquier otra pregunta.</p>
                        <span class="message-time">${new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                </div>
            `;
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, 1500);
    }

    // 6. Event listeners para el chat
    if (sendBtn && chatInput) {
        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    // 7. Botón "Volver atrás"
    const backButton = document.querySelector('.btn-secondary');
    if (backButton) {
        backButton.addEventListener('click', function() {
            window.location.href = '/modulo-solicitudes-negociacion/index.html'; // Ajusta esta ruta
        });
    }
});