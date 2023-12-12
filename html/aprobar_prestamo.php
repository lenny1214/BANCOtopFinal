<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilerbank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión a la base de datos falló: " . $conn->connect_error);
}
// Página para que el administrador apruebe o deniegue préstamos
// ... (verificar la sesión del administrador)

// Obtener préstamos pendientes
$queryPrestamos = "SELECT id, nombre_usuario, cantidad, concepto, amortizacion_meses, cuota_mensual FROM prestamos WHERE estado_aprobacion = 'Pendiente'";
$resultPrestamos = $conn->query($queryPrestamos); // Añadir el punto y coma aquí
// Después de $queryPrestamos
echo "Consulta SQL: " . $queryPrestamos . "<br>";

// Agrega esto para verificar si hay resultados en la consulta
if ($resultPrestamos->num_rows > 0) {
    // Resto del código
} else {
    echo "No hay préstamos pendientes.";
}
// Mostrar la lista de préstamos pendientes en un formulario
while ($prestamo = $resultPrestamos->fetch_assoc()) {
    echo "Nombre de usuario: " . $prestamo['nombre_usuario'] . "<br>";
    echo "Cantidad: " . $prestamo['cantidad'] . "<br>";
    // ... (mostrar otros detalles del préstamo)

    // Formulario para aprobar o denegar el préstamo
    echo "<form method='post' action='procesar_aprobacion.php'>";
    echo "<input type='hidden' name='id_prestamo' value='" . $prestamo['id'] . "'>";
    echo "<label for='estado_aprobacion'>Aprobar/Denegar:</label>";
    echo "<select name='estado_aprobacion' required>";
    echo "<option value='Aceptado'>Aceptado</option>";
    echo "<option value='Denegado'>Denegado</option>";
    echo "</select>";
    echo "<input type='submit' name='aprobar_denegar' value='Enviar'>";
    echo "</form>";

    echo "<hr>";
}
?>
