const cuerpoTabla = document.querySelector("#cuerpo-tickets");
const mensaje = document.querySelector("#mensaje");
 
function agregarCelda(fila, texto) {
    const celda = document.createElement("td");
    celda.textContent = texto;
    fila.appendChild(celda);
}

async function cargarTickets() {
    mensaje.textContent = "Cargando tickets...";
   
    try {
        const respuesta = await fetch("api/tickets.php");   

        if (!respuesta.ok) {                                 
            throw new Error("HTTP " + respuesta.status);
        }

        const resultado = await respuesta.json();            
        const tickets = resultado.datos;                     

        if (tickets.length === 0) {
            mensaje.textContent = "No hay tickets registrados.";
            return;
        }

        mensaje.textContent = "";  
 
        for (let i = 0; i < tickets.length; i++) {
            const ticket = tickets[i];
 
            let docente = "Sin asignar";
            if (ticket.nombre !== null) {
                docente = ticket.nombre + " " + ticket.apellido;
            }
 
            const fila = document.createElement("tr");
 
            agregarCelda(fila, ticket.estudiante_a_cargo);
            agregarCelda(fila, docente);
            agregarCelda(fila, ticket.hora_de_entrada);
            agregarCelda(fila, ticket.hora_de_salida);
            agregarCelda(fila, ticket.tipo_de_salon);
            agregarCelda(fila, ticket.numero_de_salon);
            agregarCelda(fila, ticket.numero_de_equipo);
            agregarCelda(fila, ticket.asignatura);
            agregarCelda(fila, ticket.grupo);
            agregarCelda(fila, ticket.turno);
            agregarCelda(fila, ticket.estado);
 
            cuerpoTabla.appendChild(fila);
        }

    } catch (error) {
        console.error(error);
        mensaje.textContent = "No se pudieron cargar los tickets.";
    }
}

cargarTickets();