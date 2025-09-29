document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formNuevaSolicitud");
    const lista = document.getElementById("misSolicitudes");

    // Cargar solicitudes al inicio
    cargarSolicitudes();

    // Manejar el formulario
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const datos = new FormData(form);

        const response = await fetch("nuevoServicio.php", {
            method: "POST",
            body: datos
        });

        const result = await response.json();

        if (result.success) {
            alert("Solicitud creada con éxito ✅");

            // 👉 Pintar la nueva solicitud visualmente
            const nueva = {
                id_solicitud: result.id,
                titulo_servicio: datos.get("titulo"),
                descripcion: datos.get("descripcion"),
                direccion_servicio: datos.get("direccion"),
                barrio: datos.get("barrio"),
                urgencia: datos.get("urgencia"),
                precio: datos.get("precio"),
                estado: "Pendiente"
            };

            renderSolicitud(nueva);
            form.reset();
        } else {
            alert("Error: " + result.message);
        }
    });

    // Función para cargar solicitudes guardadas en la BD
    async function cargarSolicitudes() {
        const res = await fetch("cliente.php");
        const data = await res.json();

        lista.innerHTML = ""; // limpiar
        data.datos.forEach(s => renderSolicitud(s));
    }

    // Función para renderizar una solicitud en la lista
    function renderSolicitud(s) {
        const item = document.createElement("div");
        item.classList.add("solicitud-card");
        item.innerHTML = `
            <h3>${s.titulo_servicio}</h3>
            <p><b>Descripción:</b> ${s.descripcion}</p>
            <p><b>Dirección:</b> ${s.direccion_servicio}, ${s.barrio}</p>
            <p><b>Urgencia:</b> ${s.urgencia}</p>
            <p><b>Precio:</b> $${s.precio}</p>
            <p><b>Estado:</b> ${s.estado}</p>
        `;
        lista.appendChild(item);
    }
});
