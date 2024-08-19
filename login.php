<?php
session_start();

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica que los campos de identificación y contraseña existan y no estén vacíos
    if (isset($_POST['identificacion']) && isset($_POST['password'])) {
        $identificacion = $_POST['identificacion'];
        $password = $_POST['password'];

        // Incluir el archivo de configuración de la base de datos
        include('db.php');

        // Consulta para verificar las credenciales
        $sql = "SELECT * FROM Usuarios WHERE Identificacion = ? AND Password = ?";
        $params = array($identificacion, $password);
        $stmt = sqlsrv_query($conn, $sql, $params);

        // Verificar si la consulta tuvo éxito y encontró el usuario
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Si se encontró un registro, establecer la sesión
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $_SESSION['identificacion'] = $row['Identificacion'];
            header("Location: inscripcion.php");
            exit();
        } else {
            echo "Credenciales inválidas";
        }
    } else {
        echo "Por favor complete todos los campos.";
    }
}
?>