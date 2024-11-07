document.getElementById('consultarForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    const formData = new FormData(this);

    fetch('http://localhost/HotelMonaco/Php/ConsultarInformaciosHuesped.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parseamos directamente como JSON
    .then(data => {
        if (data.error) {
            alert(data.error); // Mostrar alerta si hay un error
        } else {
            // Crear una ventana emergente con la información consultada
            let infoWindow = window.open("", "InfoWindow", "width=400,height=300");
            infoWindow.document.write("<h2>Información del Huésped:</h2>");
            infoWindow.document.write("<p><strong>Cédula:</strong> " + data.cedula + "</p>");
            infoWindow.document.write("<p><strong>Nombre:</strong> " + data.nombre + "</p>");
            infoWindow.document.write("<p><strong>Apellido:</strong> " + data.apellido + "</p>");
            infoWindow.document.write("<p><strong>Correo:</strong> " + data.correo + "</p>");
            infoWindow.document.write("<p><strong>Teléfono:</strong> " + data.telefono + "</p>");
            infoWindow.document.write("<p><strong>Dirección:</strong> " + data.calle_carrera + " " + data.numeroDireccion + " " + data.complementoDireccion + "</p>");
            infoWindow.document.write("<p><strong>Número de Visitas:</strong> " + data.visita + "</p>");
        }
    })
    .catch(error => console.error('Error: ', error));
});
