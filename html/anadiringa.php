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

// Inicializa el mensaje a mostrar
$mensaje_exito = '';

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'ilerbank');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_movimiento'])) {
    $tipo_movimiento = $_POST['tipo_movimiento'];
    $monto = $_POST['monto'];

    // Asegúrate de que el tipo de movimiento sea válido
    if ($tipo_movimiento !== 'ingreso' && $tipo_movimiento !== 'gasto') {
        echo "Tipo de movimiento no válido.";
        exit();
    }

    // Insertar movimiento en la tabla movimientos
    $insertMovimientoQuery = "INSERT INTO movimientos (nombre_usuario, tipo_movimiento, monto) VALUES ('{$_SESSION['nombre_usuario']}', '$tipo_movimiento', $monto)";
    $conn->query($insertMovimientoQuery);

    // Actualizar saldo del usuario en la tabla usuarios
    $updateSaldoQuery = "UPDATE usuarios SET saldo = saldo + CASE WHEN '$tipo_movimiento' = 'ingreso' THEN $monto ELSE -$monto END WHERE nombre_usuario = '{$_SESSION['nombre_usuario']}'";
    $conn->query($updateSaldoQuery);

    // Establece el mensaje de éxito
    $mensaje_exito = "El movimiento se ha registrado correctamente.";
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

 <!-- Required meta tags -->
 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>

<body>
<header>

       <!-- Navbar -->
       <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="versaldo.php">IlerBank</a>
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
    <!-- Contenido del cuerpo de la página -->
    <div class="container">
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $mensaje_exito; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="tipo_movimiento">Tipo de Movimiento:</label>
        <select name="tipo_movimiento" id="tipo_movimiento" required>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
        </select>
        <br>
        <label for="monto">Monto:</label>
        <input type="number" name="monto" id="monto" step="0.01" required>
        <br>
        <input type="submit" name="registrar_movimiento" value="Registrar Movimiento">
    </form>
</body>

</html>
