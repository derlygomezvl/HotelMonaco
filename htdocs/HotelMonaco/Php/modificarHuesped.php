<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conexion = oci_connect("system", "oracle", "localhost/xe");

$response = [];

if (!$conexion) {
    $response['error'] = "Error en la conexión a la base de datos: " . json_encode(oci_error());
    echo json_encode($response);
    exit;
}

$cedula = $_POST['cedula'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$calle_carrera = $_POST['calle_carrera'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? '';
$calle_carrera_numero = $_POST['calle_carrera_numero'] ?? '';
$visita = $_POST['visita'] ?? '';

// Manejar consulta de huésped
if (!empty($cedula) && empty($nombre)) {
    $query = "SELECT CEDULA, NOMBRE, APELLIDO, CORREO, TELEFONO, CALLE_CARRERA, NUMERODIRECCION, COMPLEMENTODIRECCION, CALLE_CARRERA_NUMERO, NUMERO_VISITA FROM huesped WHERE cedula = :cedula";
    $stid = oci_parse($conexion, $query);
    oci_bind_by_name($stid, ':cedula', $cedula);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);

    if (!$row) {
        $response['error'] = "El huésped no fue encontrado.";
    } else {
        $response['data'] = [
            'cedula' => $row['CEDULA'] ?? null,
            'nombre' => $row['NOMBRE'] ?? '',
            'apellido' => $row['APELLIDO'] ?? '',
            'correo' => $row['CORREO'] ?? '',
            'telefono' => $row['TELEFONO'] ?? '',
            'calle_carrera' => $row['CALLE_CARRERA'] ?? '',
            'numero' => $row['NUMERODIRECCION'] ?? null,
            'complemento' => $row['COMPLEMENTODIRECCION'] ?? '',
            'calle_carrera_numero' => $row['CALLE_CARRERA_NUMERO'] ?? '',
            'visita' => $row['NUMERO_VISITA'] ?? 0
        ];
    }

    oci_free_statement($stid);
}

// Manejar actualización de huésped
elseif (!empty($nombre)) {
    $query = "UPDATE huesped SET nombre = :nombre, apellido = :apellido, correo = :correo, telefono = :telefono, 
            calle_carrera = :calle_carrera, numeroDireccion = :numeroDireccion, complementoDireccion = :complementoDireccion, 
            calle_carrera_numero = :calle_carrera_numero, numero_visita = :visita WHERE cedula = :cedula";
    
    $stid = oci_parse($conexion, $query);
    
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo', $correo);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':calle_carrera', $calle_carrera);
    oci_bind_by_name($stid, ':numeroDireccion', $numero);
    oci_bind_by_name($stid, ':complementoDireccion', $complemento);
    oci_bind_by_name($stid, ':calle_carrera_numero', $calle_carrera_numero);
    oci_bind_by_name($stid, ':visita', $visita);
    oci_bind_by_name($stid, ':cedula', $cedula);
    
    if (oci_execute($stid)) {
        $response['success'] = "Información actualizada correctamente.";
    } else {
        $response['error'] = "Error al actualizar la información: " . json_encode(oci_error($stid));
    }

    oci_free_statement($stid);
}

else {
    $response['error'] = "Método no permitido o datos faltantes.";
}

oci_close($conexion);

// Devolver la respuesta en formato JSON
echo json_encode($response);
?>
