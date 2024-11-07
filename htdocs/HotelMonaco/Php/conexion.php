<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establecer conexión a la base de datos
$conexion = oci_connect("system", "oracle", "localhost/xe");

$response = [];

if (!$conexion) {
    $response['error'] = "Error en la conexión a la base de datos: " . json_encode(oci_error());
    echo json_encode($response);
    exit;
}

// Verificar si se enviaron datos
if (!isset($_POST['cedula']) || !isset($_POST['nombre']) || !isset($_POST['apellido']) || !isset($_POST['correo']) || !isset($_POST['telefono']) || !isset($_POST['calle_carrera']) || !isset($_POST['numeroDireccion'])) {
    $response['error'] = "Faltan datos en el formulario.";
    echo json_encode($response);
    exit;
}

// Recoger los datos del formulario
$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];

// Capturar los valores de la dirección
$calle_carrera = $_POST['calle_carrera'];
$calle_carrera_numero = $_POST['calle_carrera_numero']; // Captura el número de la calle o carrera
$numeroDireccion = $_POST['numeroDireccion'];
$complementoDireccion = $_POST['complementoDireccion'] ?? null;
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null; // Asegúrate de que esto esté capturando correctamente

// Verificar si la fecha de nacimiento es válida
if (!$fecha_nacimiento) {
    $response['error'] = "La fecha de nacimiento es obligatoria.";
    echo json_encode($response);
    exit;
}

// Verificar si la cédula ya está registrada
$query = "SELECT COUNT(*) AS count FROM huesped WHERE cedula = :cedula";
$stid = oci_parse($conexion, $query);
oci_bind_by_name($stid, ':cedula', $cedula);
oci_execute($stid);
$row = oci_fetch_assoc($stid);

if ($row['COUNT'] > 0) {
    $response['error'] = "El huésped ya está registrado.";
    echo json_encode($response);
    oci_free_statement($stid);
    oci_close($conexion);
    exit;
}

// Insertar el nuevo registro
$query = "INSERT INTO huesped (cedula, nombre, apellido, correo, telefono, calle_carrera, calle_carrera_numero, numeroDireccion, complementoDireccion, fecha_nacimiento, tipo_huesped, numero_visita) 
          VALUES (:cedula, :nombre, :apellido, :correo, :telefono, :calle_carrera, :calle_carrera_numero, :numeroDireccion, :complementoDireccion, TO_DATE(:fecha_nacimiento, 'YYYY-MM-DD'),'turista',0)";
$stid = oci_parse($conexion, $query);

// Vincular los parámetros
oci_bind_by_name($stid, ':cedula', $cedula);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':apellido', $apellido);
oci_bind_by_name($stid, ':correo', $correo);
oci_bind_by_name($stid, ':telefono', $telefono);
oci_bind_by_name($stid, ':calle_carrera', $calle_carrera);
oci_bind_by_name($stid, ':calle_carrera_numero', $calle_carrera_numero); // Vincula el nuevo campo
oci_bind_by_name($stid, ':numeroDireccion', $numeroDireccion);
oci_bind_by_name($stid, ':complementoDireccion', $complementoDireccion);
oci_bind_by_name($stid, ':fecha_nacimiento', $fecha_nacimiento); // Asegúrate de que esto esté correcto

// Ejecutar la consulta
if (oci_execute($stid)) {
    $response['message'] = "Registro exitoso";
} else {
    $error = oci_error($stid);
    $response['error'] = "Error al registrar el huésped: " . $error['message'];
}

oci_free_statement($stid);
oci_close($conexion);

// Enviar la respuesta en formato JSON
echo json_encode($response);
?>
