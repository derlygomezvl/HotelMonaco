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

// Verificar si se envió la cédula
if (!isset($_POST['cedula'])) {
    $response['error'] = "Falta la cédula en la consulta.";
    echo json_encode($response);
    exit;
}

// Recoger la cédula
$cedula = $_POST['cedula'];

// Consultar la información del huésped
$query = "SELECT * FROM huesped WHERE cedula = :cedula";
$stid = oci_parse($conexion, $query);
oci_bind_by_name($stid, ':cedula', $cedula);
oci_execute($stid);

$row = oci_fetch_assoc($stid);
if ($row) {
    $response = [
        'cedula' => $row['CEDULA'],
        'nombre' => $row['NOMBRE'],
        'apellido' => $row['APELLIDO'],
        'correo' => $row['CORREO'],
        'telefono' => $row['TELEFONO'],
        'calle_carrera' => $row['CALLE_CARRERA'],
        'numeroDireccion' => $row['NUMERODIRECCION'],
        'complementoDireccion' => $row['COMPLEMENTODIRECCION'],
        'visita' => $row['NUMERO_VISITA'],
    ];
} else {
    $response['error'] = "El huésped no fue encontrado.";
}

// Liberar recursos
oci_free_statement($stid);
oci_close($conexion);

// Devolver la respuesta JSON
echo json_encode($response);
?>
