<?php
session_start();

// Verifica si se ha enviado el formulario de cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión
    session_destroy();
    header('Location: login.php');
    exit();
}

// Verifica si el usuario no ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
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
$query = "SELECT foto_perfil FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($foto_perfil);
$stmt->fetch();
$stmt->close();

// Obtener saldo del usuario
$saldoQuery = "SELECT SUM(CASE WHEN tipo_movimiento = 'ingreso' THEN monto ELSE -monto END) AS saldo 
               FROM movimientos WHERE nombre_usuario = ?";
$stmtSaldo = $conn->prepare($saldoQuery);
$stmtSaldo->bind_param('s', $username);
$stmtSaldo->execute();
$stmtSaldo->bind_result($saldo);
$stmtSaldo->fetch();
$stmtSaldo->close();

// Obtener últimos 10 movimientos del usuario
$movimientosQuery = "SELECT * FROM movimientos WHERE nombre_usuario = ? ORDER BY fecha_movimiento DESC LIMIT 10";
$stmtMovimientos = $conn->prepare($movimientosQuery);
$stmtMovimientos->bind_param('s', $username);
$stmtMovimientos->execute();
$movimientosResult = $stmtMovimientos->get_result();

// Obtener detalles del préstamo
$prestamoQuery = "SELECT * FROM prestamos WHERE nombre_usuario = ? ORDER BY fecha_solicitud DESC LIMIT 1";
$stmtPrestamo = $conn->prepare($prestamoQuery);
$stmtPrestamo->bind_param('s', $username);
$stmtPrestamo->execute();
$prestamoResult = $stmtPrestamo->get_result();

// Cerrar las declaraciones preparadas
$stmtMovimientos->close();
$stmtPrestamo->close();

// Cerrar conexión (después de haber terminado de trabajar con todas las consultas)
$conn->close();
?>
<!-- Resto de tu código -->


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
            echo "<li>{$movimiento['tipo_movimiento']} {$signo} {$movimiento['monto']} - {$movimiento['fecha_movimiento']}</li>";
        }
        ?>
<h2>Datos del Préstamo</h2>
         <?php
       $prestamoQuery = "SELECT * FROM prestamos WHERE nombre_usuario = ? ORDER BY fecha_solicitud DESC LIMIT 1";
       $stmtPrestamo = $conn->prepare($prestamoQuery);
       $stmtPrestamo->bind_param('s', $username);
       $stmtPrestamo->execute();
       $prestamoResult = $stmtPrestamo->get_result();
       $stmtPrestamo->close();
       
       if ($prestamo = $prestamoResult->fetch_assoc()) {
           echo "<p>Cantidad: {$prestamo['cantidad']}</p>";
           echo "<p>Concepto: {$prestamo['concepto']}</p>";
           echo "<p>Amortización en meses: {$prestamo['amortizacion_meses']}</p>";
           echo "<p>Cuota Mensual: {$prestamo['cuota_mensual']}</p>";
           echo "<p>Fecha de Solicitud: {$prestamo['fecha_solicitud']}</p>";
       } else {
           echo "<p>No hay préstamos solicitados.</p>";
       }
       
        ?>

        <!-- Fin de la sección de detalles del préstamo -->
        <!-- Mostrar la foto de perfil -->
        <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil">
    </div>
</main>

<footer>
    <!-- place footer here -->
</footer>
</body>
</html>
