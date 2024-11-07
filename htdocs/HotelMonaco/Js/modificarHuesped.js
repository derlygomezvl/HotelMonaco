document.getElementById("modificarForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Evitar el envío del formulario

    const cedula = document.getElementById("cedula").value.trim();
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const calle_carrera = document.getElementById("calle_carrera").value;
    const numero = document.getElementById("numero").value.trim();
    const complemento = document.getElementById("complemento").value.trim();
    const calle_carrera_numero = document.getElementById("calle_carrera_numero").value.trim();
    const visita = document.getElementById("visita").value;

    // Validaciones
    const mensajeAdvertencia = document.getElementById("mensajeAdvertencia");
    mensajeAdvertencia.style.display = "none"; // Ocultar mensaje de advertencia

    if (!validarCedula(cedula)) {
        mostrarAdvertencia("La cédula debe ser un número positivo.");
        return;
    }

    if (!validarNombreApellido(nombre) || !validarNombreApellido(apellido)) {
        mostrarAdvertencia("El nombre y el apellido no deben contener caracteres especiales.");
        return;
    }

    if (!validarCorreo(correo)) {
        mostrarAdvertencia("El correo electrónico no es válido.");
        return;
    }

    if (!validarTelefono(telefono)) {
        mostrarAdvertencia("El número de teléfono debe contener solo números.");
        return;
    }

    if (visita < 0) {
        mostrarAdvertencia("El número de visita no puede ser negativo.");
        return;
    }

    // Enviar la solicitud al servidor
    enviarDatos(cedula, nombre, apellido, correo, telefono, calle_carrera, numero, complemento, calle_carrera_numero, visita);
});

document.getElementById("consultarButton").addEventListener("click", function() {
    const cedula = document.getElementById("cedula").value.trim();

    if (!validarCedula(cedula)) {
        mostrarAdvertencia("La cédula debe ser un número positivo.");
        return;
    }

    // Enviar solicitud de consulta
    fetchData(cedula);
});

function validarCedula(cedula) {
    return /^\d+$/.test(cedula) && parseInt(cedula) > 0;
}

function validarNombreApellido(texto) {
    return /^[a-zA-Z\s]+$/.test(texto);
}

function validarCorreo(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

function validarTelefono(telefono) {
    return /^\d*$/.test(telefono);
}

function mostrarAdvertencia(mensaje) {
    const mensajeAdvertencia = document.getElementById("mensajeAdvertencia");
    mensajeAdvertencia.textContent = mensaje;
    mensajeAdvertencia.style.display = "block"; // Mostrar mensaje de advertencia
}

function enviarDatos(cedula, nombre, apellido, correo, telefono, calle_carrera, numero, complemento, calle_carrera_numero, visita) {
    const formData = new FormData();
    formData.append("cedula", cedula);
    formData.append("nombre", nombre);
    formData.append("apellido", apellido);
    formData.append("correo", correo);
    formData.append("telefono", telefono);
    formData.append("calle_carrera", calle_carrera);
    formData.append("numero", numero);
    formData.append("complemento", complemento);
    formData.append("calle_carrera_numero", calle_carrera_numero);
    formData.append("visita", visita);

    fetch("http://localhost/HotelMonaco/Php/modificarHuesped.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            mostrarAdvertencia(data.error);
        } else {
            alert(data.success || "Operación realizada exitosamente.");
            // Aquí puedes limpiar los campos si lo deseas
        }
    })
    .catch(error => {
        mostrarAdvertencia("Error en la consulta: " + error);
    });
}

function fetchData(cedula) {
    const formData = new FormData();
    formData.append("cedula", cedula);

    fetch("http://localhost/HotelMonaco/Php/modificarHuesped.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            mostrarAdvertencia(data.error);
        } else {
            // Rellenar los campos con la información del huésped
            document.getElementById("nombre").value = data.data.nombre;
            document.getElementById("apellido").value = data.data.apellido;
            document.getElementById("correo").value = data.data.correo;
            document.getElementById("telefono").value = data.data.telefono;
            document.getElementById("calle_carrera").value = data.data.calle_carrera;
            document.getElementById("numero").value = data.data.numero;
            document.getElementById("complemento").value = data.data.complemento;
            document.getElementById("calle_carrera_numero").value = data.data.calle_carrera_numero;
            document.getElementById("visita").value = data.data.visita;

            // Bloquear el campo de cédula
            document.getElementById("cedula").setAttribute("readonly", true);
        }
    })
    .catch(error => {
        mostrarAdvertencia("Error en la consulta: " + error);
    });
}

// Limpiar campos y desbloquear cédula para nueva búsqueda
document.getElementById("nuevaBusquedaButton").addEventListener("click", function() {
    document.getElementById("modificarForm").reset();
    document.getElementById("cedula").removeAttribute("readonly"); // Desbloquear cédula
    document.getElementById("mensajeAdvertencia").style.display = "none"; // Ocultar mensaje de advertencia
});
