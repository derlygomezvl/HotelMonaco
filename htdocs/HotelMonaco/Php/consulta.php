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

// Verificar si la conexión fue exitosa
if (!$conexion) {
    $e = oci_error();
    $response['error'] = "No se pudo conectar a la base de datos: " . htmlentities($e['message'], ENT_QUOTES);
    echo json_encode($response);
    exit;
}

// Verificar si se recibió la cédula
if (isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];

    // Preparar la consulta
    $query = "SELECT h.cedula, h.nombre, h.apellido, h.correo, h.telefono, h.calle_carrera, h.numeroDireccion, h.complementoDireccion, 
                     COUNT(v.idVisita) AS visita, r.numeroHabitacion, r.estadoHabitacion 
              FROM Huesped h 
              LEFT JOIN Visita v ON h.cedula = v.cedula 
              LEFT JOIN Habitacion r ON h.cedula = r.cedula
              WHERE h.cedula = :cedula 
              GROUP BY h.cedula, h.nombre, h.apellido, h.correo, h.telefono, h.calle_carrera, h.numeroDireccion, h.complementoDireccion, r.numeroHabitacion, r.estadoHabitacion";

    $stid = oci_parse($conexion, $query);
    oci_bind_by_name($stid, ':cedula', $cedula);
    
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);

    if ($row) {
        // Información del huésped encontrada
        $response = [
            'cedula' => $row['CEDULA'],
            'nombre' => $row['NOMBRE'],
            'apellido' => $row['APELLIDO'],
            'correo' => $row['CORREO'],
            'telefono' => $row['TELEFONO'],
            'calle_carrera' => $row['CALLE_CARRERA'],
            'numeroDireccion' => $row['NUMERODIRECCION'],
            'complementoDireccion' => $row['COMPLEMENTODIRECCION'],
            'visita' => $row['VISITA'],
            'habitacion' => $row['NUMEROHABITACION'],
            'estadoHabitacion' => $row['ESTADOHABITACION']
        ];
    } else {
        // No se encontraron datos
        $response['error'] = "No se encontraron datos para la cédula ingresada.";
    }

    oci_free_statement($stid);
} else {
    $response['error'] = "Cédula no proporcionada.";
}

// Cerrar la conexión
oci_close($conexion);

// Devolver la respuesta en formato JSON
echo json_encode($response);