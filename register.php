<?php
// Incluir el archivo de configuración
include('db.php');

// Obtener datos del formulario
$identificacion = $_POST['identificacion'];

$password = $_POST['password'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$idCarrera = $_POST['idCarrera'];
$direccion = $_POST['direccion'];
$correoElectronico = $_POST['correoElectronico'];

if ($identificacion && $password && $nombre && $apellidos && $idCarrera && $correoElectronico) {
    // Consulta SQL para insertar un nuevo usuario
    $sql = "INSERT INTO Usuarios (Identificacion, IDRol, Password, Nombre, Apellidos, Telefono, IDCarrera, IDGrupo, Direccion, CorreoElectronico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array($identificacion, 'user', $password, $nombre, $apellidos, $telefono, $idCarrera, 'ASIG', $direccion, $correoElectronico);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si la inserción fue exitosa
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Registro exitoso";
        header('Location: index.html');
        exit();
    }
} else {
    echo "Faltan campos obligatorios.";
}
// Cerrar la conexión
sqlsrv_close($conn);
?>
