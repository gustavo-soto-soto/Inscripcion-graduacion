<?php
$serverName = "NARROWMINK\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "DB_OpcionesGraduacion",
    "Uid" => "admin",
    "PWD" => "Admin1234"
);

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
