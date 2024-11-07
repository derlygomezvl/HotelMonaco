document.getElementById('registroForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    let formIsValid = true;
    let messages = [];

    const cedula = this.cedula.value.trim();
    const nombre = this.nombre.value.trim();
    const apellido = this.apellido.value.trim();
    const correo = this.correo.value.trim();
    const telefono = this.telefono.value.trim();
    const calle_carrera = this.calle_carrera.value.trim();
    const calle_carrera_numero = this.calle_carrera_numero.value.trim(); // Campo añadido
    const numero = this.numeroDireccion.value.trim(); // Cambiado para coincidir con el nombre correcto
    const complemento = this.complementoDireccion.value.trim();
    const fecha_nacimiento = this.fecha_nacimiento.value.trim();
    const promociones = this.promociones.checked;

    // Validar campos vacíos
    if (!cedula || !nombre || !apellido || !correo || !telefono || !calle_carrera || !calle_carrera_numero || !numero || !fecha_nacimiento) {
        formIsValid = false;
        messages.push("Por favor, complete todos los campos obligatorios.");
    }

    // Validar cédula (no debe ser negativa)
    if (cedula < 0) {
        formIsValid = false;
        messages.push("La cédula no puede ser negativa.");
    }

    // Validar el nombre y apellido (sin caracteres especiales)
    const namePattern = /^[a-zA-Z\s]+$/;
    if (nombre && !namePattern.test(nombre)) {
        formIsValid = false;
        messages.push("El nombre no puede contener caracteres especiales.");
    }
    if (apellido && !namePattern.test(apellido)) {
        formIsValid = false;
        messages.push("El apellido no puede contener caracteres especiales.");
    }

    // Validar el correo electrónico
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (correo && !emailPattern.test(correo)) {
        formIsValid = false;
        messages.push("Por favor, ingrese un correo electrónico válido.");
    }

    // Validar términos y condiciones
    if (!promociones) {
        formIsValid = false;
        messages.push("Debe aceptar los términos y condiciones.");
    }

    // Validar la fecha de nacimiento para asegurarse de que sea mayor de 18 años
    const today = new Date();
    const dob = new Date(fecha_nacimiento);
    const minDate = new Date(today.setFullYear(today.getFullYear() - 18)); // Fecha de hace 18 años
    if (dob > minDate) {
        formIsValid = false;
        messages.push("Debes tener al menos 18 años.");
    }

    // Si el formulario no es válido, evitar el envío y mostrar mensajes
    if (!formIsValid) {
        showAlert(messages); // Muestra los mensajes de error
        return; // No continuar con la ejecución
    }

    // Limpiar mensajes previos antes de enviar
    showAlert([]); // Limpia los mensajes

    // Si el formulario es válido, enviar los datos al archivo PHP usando Fetch API
    const formData = new FormData(this);

    fetch('http://localhost/HotelMonaco/Php/conexion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(json => {
        if (json.error) {
            showAlert([json.error]); // Mostrar el error del servidor
        } else {
            showAlert([json.message], true); // Mostrar mensaje de éxito
            setTimeout(() => {
                location.reload(); // Recargar la página después de 2 segundos
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error: ', error);
        showAlert(["Ocurrió un error al enviar el formulario. Por favor, inténtelo de nuevo."]);
    });
});

// Función para mostrar alertas de forma estilizada
function showAlert(messages, isSuccess = false) {
    const messageContainer = document.getElementById('messageContainer');
    messageContainer.innerHTML = messages.map(message => 
        `<div class="alert ${isSuccess ? 'alert-success' : 'alert-danger'}" role="alert">${message}</div>`).join('');
}
