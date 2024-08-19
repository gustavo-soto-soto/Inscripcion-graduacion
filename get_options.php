<?php
// Incluir el archivo de configuración
include('db.php');

// Consulta SQL para obtener las opciones de graduación
$sql = "SELECT IDOpcion, NombreOpcion FROM OpcionesGraduacion";
$stmt = sqlsrv_query($conn, $sql);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Crear un array para almacenar las opciones
$options = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $options[] = $row;
}

// Cerrar la conexión
sqlsrv_close($conn);

// Devolver las opciones en formato JSON
echo json_encode($options);
?>
