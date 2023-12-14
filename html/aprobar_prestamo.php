<?php

session_start();

// Verifica si se ha enviado el formulario de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión
    session_destroy();
    header('Location: login.php');
    exit();
}
// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Ign@fervig12";
$dbname = "ilerbank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión a la base de datos falló: " . $conn->connect_error);
}
// Página para que el administrador apruebe o deniegue préstamos
// ... (verificar la sesión del administrador)

// Obtener préstamos pendientes
$queryPrestamos = "SELECT id, nombre_usuario, cantidad, concepto, amortizacion, cuota_amortizacion FROM prestamos WHERE estado_aprobacion = 'Pendiente'";
$resultPrestamos = $conn->query($queryPrestamos); // Añadir el punto y coma aquí

// Agrega esto para verificar si hay resultados en la consulta

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamo Denegado</title>
    <head>
    <title>Movimientos</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
            integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
            integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous"></script>
</head></head>
<body>
<header>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="indexadmin.php">IlerBank</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="indexadmin.php">Volver</a>
                        </li>
                      
                <form class="d-flex" method="post" action="">
                    <input class="btn btn-outline-danger" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
                </form>
            </div>
        </nav>
  </header>
    <?php

if ($resultPrestamos->num_rows > 0) {
} else {
    echo "No hay préstamos pendientes.";
}
// Mostrar la lista de préstamos pendientes en un formulario
while ($prestamo = $resultPrestamos->fetch_assoc()) {
    echo "Nombre de usuario: " . $prestamo['nombre_usuario'] . "<br>";
    echo "Cantidad: " . $prestamo['cantidad'] . "<br>";

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
</body>
</html>

