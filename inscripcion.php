<?php
session_start(); // Asegúrate de que la sesión esté iniciada

// Incluir el archivo de configuración para la conexión a la base de datos
include('db.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['identificacion'])) {
    header("Location: login.php");
    exit();
}

// Obtener la identificación del usuario logueado desde la sesión
$identificacion = $_SESSION['identificacion'];

// Obtener los datos del usuario logueado desde la base de datos
$sql = "SELECT Nombre, Apellidos, CorreoElectronico, Telefono, IDCarrera FROM Usuarios WHERE Identificacion = ?";
$params = array($identificacion);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Precargar los datos del usuario
$nombre = $userData['Nombre'];
$apellidos = $userData['Apellidos'];
$correo = $userData['CorreoElectronico'];
$telefono = $userData['Telefono'];
$idCarrera = $userData['IDCarrera'];

// Lógica del formulario solo se ejecuta cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la opción seleccionada
    $idOpcion = $_POST['idOpcion'];

    // Generar un nuevo IDSolicitud único
    do {
        $idSolicitud = uniqid('SOL'); // Generar un ID único con prefijo 'SOL'
        $sqlCheck = "SELECT COUNT(*) as count FROM Solicitudes WHERE IDSolicitud = ?";
        $paramsCheck = array($idSolicitud);
        $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
        $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
    } while ($rowCheck['count'] > 0);

    // Insertar en la tabla Solicitudes
    $sqlInsertSolicitud = "INSERT INTO Solicitudes (IDSolicitud, Identificacion, Observaciones, Estado) VALUES (?, ?, NULL, 'P')";
    $paramsInsertSolicitud = array($idSolicitud, $identificacion);
    $stmtInsertSolicitud = sqlsrv_query($conn, $sqlInsertSolicitud, $paramsInsertSolicitud);

    if ($stmtInsertSolicitud === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Insertar en la tabla Formulario
    $sqlInsertFormulario = "INSERT INTO Formulario (IDSolicitud, IDOpcion, IDCarrera, CedulaJuridicaEmpresa, NombreEmpresa, Ubicacion) VALUES (?, ?, ?, NULL, NULL, NULL)";
    $paramsInsertFormulario = array($idSolicitud, $idOpcion, $idCarrera);
    $stmtInsertFormulario = sqlsrv_query($conn, $sqlInsertFormulario, $paramsInsertFormulario);

    if ($stmtInsertFormulario === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener la descripción de la opción de graduación seleccionada
    $sqlOpcionDescripcion = "SELECT NombreOpcion FROM OpcionesGraduacion WHERE IDOpcion = ?";
    $paramsOpcion = array($idOpcion);
    $stmtOpcion = sqlsrv_query($conn, $sqlOpcionDescripcion, $paramsOpcion);

    if ($stmtOpcion === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $opcionGraduacion = sqlsrv_fetch_array($stmtOpcion, SQLSRV_FETCH_ASSOC)['NombreOpcion'];

    // Mostrar un reporte con los datos guardados
    echo "<div class='report'>";
    echo "<h2>Reporte de Inscripción</h2>";
    echo "<p><strong>ID Solicitud:</strong> $idSolicitud</p>";
    echo "<p><strong>Nombre:</strong> $nombre</p>";
    echo "<p><strong>Apellidos:</strong> $apellidos</p>";
    echo "<p><strong>Correo Electrónico:</strong> $correo</p>";
    echo "<p><strong>Teléfono:</strong> $telefono</p>";
    echo "<p><strong>Carrera:</strong> $idCarrera</p>";
    echo "<p><strong>Opción de Graduación Seleccionada:</strong> $opcionGraduacion</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            color: #007AA2;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #009DCF;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #007AA2;
        }

        .report {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .report p {
            margin: 10px 0;
        }

        .report strong {
            color: #007AA2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Inscripción a Opción de Graduación</h2>
        <form action="inscripcion.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" readonly>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" readonly>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" readonly>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" readonly>

            <label for="carrera">Carrera:</label>
            <input type="text" id="carrera" name="carrera" value="<?php echo htmlspecialchars($idCarrera); ?>" readonly>

            <label for="idOpcion">Opción de Graduación:</label>
            <select id="idOpcion" name="idOpcion">
                <?php
                // Cargar las opciones de graduación desde la base de datos
                $sqlOpciones = "SELECT IDOpcion, NombreOpcion FROM OpcionesGraduacion";
                $stmtOpciones = sqlsrv_query($conn, $sqlOpciones);

                while ($opcion = sqlsrv_fetch_array($stmtOpciones, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$opcion['IDOpcion']}'>{$opcion['NombreOpcion']}</option>";
                }
                ?>
            </select>

            <input type="submit" value="Enviar">
        </form>
    </div>
</body>

</html>
