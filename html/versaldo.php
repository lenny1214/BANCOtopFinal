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

// Obtener saldo del usuario
$saldoQuery = "SELECT SUM(CASE WHEN tipo_movimiento = 'ingreso' THEN monto ELSE -monto END) AS saldo 
               FROM movimientos WHERE nombre_usuario = '{$_SESSION['nombre_usuario']}'";
$saldoResult = $conn->query($saldoQuery);

if ($saldoResult) {
    $saldoRow = $saldoResult->fetch_assoc();
    $saldo = $saldoRow['saldo'];
} else {
    $saldo = 0;
}

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
  // No te deja acceder al index.php ya que no hay ninguna sesión iniciada
  session_destroy();
  header('Location: login.php');
  exit();
}

// Obtener últimos 10 movimientos del usuario
$movimientosQuery = "SELECT * FROM movimientos WHERE nombre_usuario = '{$_SESSION['nombre_usuario']}' ORDER BY fecha_movimiento DESC LIMIT 10";
$movimientosResult = $conn->query($movimientosQuery);

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<title>Movimientos</title>
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

    <main>
    <h2>Bienvenido,
      <?php echo $_SESSION['nombre_usuario']; ?>!
    </h2>
    <form method="post" action="">
      <input type="submit" name="cerrar_sesion" value="Cerrar Sesión">
    </form>    
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
        </div>
    </main>

    <footer>
        <!-- place footer here -->
    </footer>
</body>

</html>
