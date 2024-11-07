document.getElementById('consultaHabitacionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const cedula = document.getElementById('cedula').value;

    // Validación básica en el cliente
    if (!/^\d+$/.test(cedula)) {
        alert("Por favor, ingrese un número de cédula válido.");
        return;
    }

    const formData = new FormData();
    formData.append('cedula', cedula);

    fetch('consulta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const resultadoDiv = document.getElementById('resultado');

        if (data.error) {
            resultadoDiv.innerHTML = <p style="color: red;">${data.error}</p>;
        } else {
            resultadoDiv.innerHTML = `
                <p><strong>Cédula:</strong> ${data.cedula}</p>
                <p><strong>Nombre:</strong> ${data.nombre} ${data.apellido}</p>
                <p><strong>Correo:</strong> ${data.correo}</p>
                <p><strong>Teléfono:</strong> ${data.telefono}</p>
                <p><strong>Dirección:</strong> ${data.calle_carrera} ${data.numeroDireccion}, ${data.complementoDireccion}</p>
                <p><strong>Número de visitas:</strong> ${data.visita}</p>
                <p><strong>Habitación:</strong> ${data.habitacion}</p>
                <p><strong>Estado de la habitación:</strong> ${data.estadoHabitacion}</p>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});