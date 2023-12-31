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
$conn = new mysqli('localhost', 'root', 'Ign@fervig12', 'ilerbank');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se ha enviado el formulario de solicitud de préstamo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_prestamo'])) {
    $username = $_SESSION['nombre_usuario'];
    $cantidad = $_POST['cantidad'];
    $concepto = $_POST['concepto'];
    $amortizacion = $_POST['amortizacion'];
    $interes = 0.12; // 0.12 = 12%

    // Obtener saldo del usuario
    $saldoQuery = "SELECT saldo FROM usuarios WHERE nombre_usuario = ?";
    $stmt = $conn->prepare($saldoQuery);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($saldo);
    $stmt->fetch();
    $stmt->close();

    // Verificar si el saldo es suficiente
    $saldo_suficiente = $saldo >= 1000;

    if ($saldo_suficiente) {
        // Calcula la cuota de amortización
        $cuota_amortizacion = ($cantidad * $interes) / (1 - pow(1 + $interes, -$amortizacion));

        // Inserta el préstamo en la base de datos
        $insertPrestamoQuery = "INSERT INTO prestamos (nombre_usuario, cantidad, concepto, amortizacion, cuota_amortizacion, fecha_prestamo) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertPrestamoQuery);
        $stmt->bind_param('sdsds', $username, $cantidad, $concepto, $amortizacion, $cuota_amortizacion);
        $stmt->execute();
        $stmt->close();

        // Cierra la conexión
        $conn->close();

        // Redirige a versaldo.php después de la solicitud de préstamo
        header('Location: versaldo.php');
        exit();
    } else {
        $mensaje_error = "No tienes el saldo necesario para solicitar un préstamo.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Solicitud de Préstamo - IlerBank</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="versaldo.php">
                    <img src="../img/logoBanco.png" alt="Logo del Banco" height="40" class="d-inline-block align-text-top">Inicio
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
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

    <main class="container mt-3">
        <h2 class="mb-4">Solicitud de Préstamo</h2>
        <?php
        if (isset($mensaje_error)) {
            echo "<p class='text-danger'>$mensaje_error</p>";
        }
        ?>
        <form method="post" action="" class="row g-3">
            <div class="col-md-6">
                <label for="cantidad" class="form-label">Cantidad:</label>
                <input type="number" name="cantidad" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="concepto" class="form-label">Concepto:</label>
                <input type="text" name="concepto" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="amortizacion" class="form-label">Amortización (meses):</label>
                <input type="number" name="amortizacion" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" name="solicitar_prestamo" class="btn btn-primary">Solicitar Préstamo</button>
            </div>
        </form>
        
    </main>

    <footer class="mt-4 bg-light text-center p-3">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> Banco Ilerbank. Todos los derechos reservados.
            </p>
            <p class="mb-0">
                <a href="terminos_y_condiciones.php" class="text-decoration-none">Términos y Condiciones</a>
            </p>
        </div>
    </footer>
</body>

</html>