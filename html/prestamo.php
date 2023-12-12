<?php
session_start();

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
$query = "SELECT saldo FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($saldo);
$stmt->fetch();
$stmt->close();

// Verificar si el saldo es mayor que 1000 para permitir solicitar el préstamo
if ($saldo < 1000) {
    header('Location: versaldo.php'); // Redirigir a la página de saldo si el saldo es insuficiente
    exit();
}

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_prestamo'])) {
    $cantidad = $_POST['cantidad'];
    $concepto = $_POST['concepto'];
    $amortizacion_meses = $_POST['amortizacion_meses'];

    // Asegúrate de que los campos no estén vacíos
    if (empty($cantidad) || empty($concepto) || empty($amortizacion_meses)) {
        $error_message = "Todos los campos son obligatorios.";
    } else {
        // Calcular interés del 0.7%
        $interes = 0.007; // 0.7%

        // Calcular la cuota mensual del préstamo
        $cuota_mensual = ($cantidad * $interes) / (1 - pow(1 + $interes, -$amortizacion_meses));

        // Insertar préstamo en la tabla prestamos
        $insertPrestamoQuery = "INSERT INTO prestamos (nombre_usuario, cantidad, concepto, amortizacion_meses, cuota_mensual) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertPrestamoQuery);
        $stmt->bind_param('sissd', $username, $cantidad, $concepto, $amortizacion_meses, $cuota_mensual);
        $stmt->execute();
        $stmt->close();

        // Actualizar saldo del usuario en la tabla usuarios
        $updateSaldoQuery = "UPDATE usuarios SET saldo = saldo + $cantidad WHERE nombre_usuario = ?";
        $stmt = $conn->prepare($updateSaldoQuery);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->close();

        // Redirigir o mostrar mensaje de éxito, según tus necesidades
        header('Location: versaldo.php');
        exit();
    }
}

// Cerrar conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
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
    <!-- ... Tu código navbar ... -->
</header>

<!-- Contenido del cuerpo de la página -->
<div class="container">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" step="0.01" required>
        <br>
        <label for="concepto">Concepto:</label>
        <input type="text" name="concepto" id="concepto" required>
        <br>
        <label for="amortizacion_meses">Amortización en Meses:</label>
        <input type="number" name="amortizacion_meses" id="amortizacion_meses" required>
        <br>
        <input type="submit" name="solicitar_prestamo" value="Solicitar Préstamo">
    </form>
</div>

</body>

</html>
