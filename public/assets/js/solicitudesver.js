const cuerpoTabla = document.querySelector("#cuerpo-solicitudes");
const mensaje = document.querySelector("#mensaje");
 
function agregarCelda(fila, texto) {
    const celda = document.createElement("td");
    celda.textContent = texto;
    fila.appendChild(celda);
}

async function cargarSolicitudes() {
    mensaje.textContent = "Cargando solicitudes...";
   
    try {
        const respuesta = await fetch("api/solicitudes.php");   

        if (!respuesta.ok) {                                 
            throw new Error("HTTP " + respuesta.status);
        }

        const resultado = await respuesta.json();            
        const solicitudes = resultado.datos;                     

        if (solicitudes.length === 0) {
            mensaje.textContent = "No hay solicitudes registradas.";
            return;
        }

        mensaje.textContent = "";  
 
        for (let i = 0; i < solicitudes.length; i++) {
            const solicitud = solicitudes[i];
 
            const fila = document.createElement("tr");
 
            agregarCelda(fila, solicitud.id_solicitud);
            agregarCelda(fila, solicitud.descripcion);
            agregarCelda(fila, solicitud.nombre + " " + solicitud.apellido);
            agregarCelda(fila, solicitud.fecha_solicitada);
 
            cuerpoTabla.appendChild(fila);
        }

    } catch (error) {
        console.error(error);
        mensaje.textContent = "No se pudieron cargar las solicitudes.";
    }
}

cargarSolicitudes();