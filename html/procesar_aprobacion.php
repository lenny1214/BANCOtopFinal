<?php
// procesar_aprobacion.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aprobar_denegar'])) {
    // Obtén los datos del formulario
    $id_prestamo = $_POST['id_prestamo'];
    $estado_aprobacion = $_POST['estado_aprobacion'];

    // Realiza el procesamiento de la aprobación/denegación aquí
    // ...

    // Actualiza el estado del préstamo en la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "Ign@fervig12";
    $dbname = "ilerbank";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $updateQuery = "UPDATE prestamos SET estado_aprobacion = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $estado_aprobacion, $id_prestamo);
    
    if ($stmt->execute()) {
        // Redirige a una página específica después de procesar
        if ($estado_aprobacion === 'Aceptado') {
            header('Location: prestamo_aprobado.php');
        } elseif ($estado_aprobacion === 'Denegado') {
            header('Location: prestamo_denegado.php');
        } else {
            header('Location: error.php'); // En caso de algún error inesperado
        }
    } else {
        echo "Error al actualizar el estado del préstamo: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: error.php'); // Redirige si no es una solicitud POST válida
}
?>
