<?php
session_start();

// Verifica si se ha enviado el formulario de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión
    session_destroy();
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'ilerbank');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener información del usuario
$username = $_SESSION['nombre_usuario'];

// Obtener saldo del usuario
$saldoQuery = "SELECT SUM(CASE WHEN tipo_movimiento = 'ingreso' THEN monto ELSE -monto END) AS saldo 
               FROM movimientos WHERE nombre_usuario = ?";
$stmt = $conn->prepare($saldoQuery);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

// Obtener últimos 10 movimientos del usuario
$movimientosQuery = "SELECT * FROM movimientos WHERE nombre_usuario = ? ORDER BY fecha_movimiento DESC LIMIT 10";
$stmt = $conn->prepare($movimientosQuery);
$stmt->bind_param('s', $username);
$stmt->execute();
$movimientosResult = $stmt->get_result();
$stmt->close();

// Obtener historial de préstamos del usuario
$prestamosQuery = "SELECT * FROM prestamos WHERE nombre_usuario = ?";
$stmt = $conn->prepare($prestamosQuery);
$stmt->bind_param('s', $username);
$stmt->execute();
$prestamosResult = $stmt->get_result();
$stmt->close();

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
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
</head>

<body>
<header>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="versaldo.php.php">IlerBank</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="anadiringa.php">Añadir Ingreso/Gasto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="generariban.php">Generar IBAN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prestamo.php">Pedir Préstamo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="chat.php">Chat</a>
                        </li>
                    </ul>
                </div>
                <form class="d-flex" method="post" action="">
                    <input class="btn btn-outline-danger" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
                </form>
            </div>
        </nav>
  </header>

<main>
    <h2>Bienvenido,
        <?php echo $_SESSION['nombre_usuario']. ". Hoy es: " . date ('d \d\e F \d\e Y'); ?>!
    </h2>

    <div style="padding: 16px;">
        <h2>Saldo Actual</h2>
        <p>Saldo: <?php echo $saldo; ?></p>

        <h2>Últimos 10 Movimientos</h2>
<?php
while ($movimiento = $movimientosResult->fetch_assoc()) {
    $signo = ($movimiento['tipo_movimiento'] == 'ingreso') ? '+' : '-';
    $monto = isset($movimiento['monto']) ? $movimiento['monto'] : 0;
    echo "<li>{$movimiento['tipo_movimiento']} {$signo} {$monto} - {$movimiento['fecha_movimiento']}</li>";
}
?>

<h2>Historial de Préstamos</h2>
<?php
if ($prestamosResult->num_rows > 0) {
    while ($prestamo = $prestamosResult->fetch_assoc()) {
        $cantidadPrestamo = isset($prestamo['cantidad']) ? $prestamo['cantidad'] : 0;
        echo "<li>Prestamo: {$cantidadPrestamo} - {$prestamo['fecha_prestamo']} - Estado: {$prestamo['estado_aprobacion']}</li>";
    }
} else {
    echo "<p>No has solicitado ningún préstamo.</p>";
}
?>


        <!-- Mostrar la foto de perfil -->
        <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil">
    </div>
</main>

<footer>
    <!-- place footer here -->
</footer>
</body>
</html>
