<?php
session_start();

// Verifica si se ha enviado el formulario de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión
    session_destroy();
    header('Location: login.php');
    exit();
}

// Verifica si el usuario no ha iniciado sesión o no es administrador
if (!isset($_SESSION['nombre_usuario']) || !$_SESSION['es_administrador']) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'ilerbank');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se ha enviado el formulario de acciones de préstamo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_prestamo'])) {
    $accion_prestamo_id = $_POST['accion_prestamo'];

    // Obtiene la acción realizada por el administrador
    $accion = $_POST['accion'];

    // Realiza las operaciones necesarias según la acción
    if ($accion === 'aceptar') {
        // Actualiza el estado del préstamo a 'Aceptado'
        $updateEstadoQuery = "UPDATE prestamos SET estado = 'Aceptado' WHERE id = ?";
        $stmt = $conn->prepare($updateEstadoQuery);
        $stmt->bind_param('i', $accion_prestamo_id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'denegar') {
        // Actualiza el estado del préstamo a 'Denegado'
        $updateEstadoQuery = "UPDATE prestamos SET estado = 'Denegado' WHERE id = ?";
        $stmt = $conn->prepare($updateEstadoQuery);
        $stmt->bind_param('i', $accion_prestamo_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirige de nuevo a la página de administración de préstamos
    header('Location: administrar_prestamos.php');
    exit();
}

// Cerrar conexión
$conn->close();
?>