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
$conn = new mysqli('localhost', 'root', 'Ign@fervig12', 'ilerbank');

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
        // Lógica para aceptar el préstamo
        // Actualiza el estado del préstamo a 'Aceptado'
        // ...

    } elseif ($accion === 'denegar') {
        // Lógica para denegar el préstamo
        // Actualiza el estado del préstamo a 'Denegado'
        // ...
    }

    // Redirige de nuevo a la página de administración de préstamos
    header('Location: administrar_prestamos.php');
    exit();
}

// Realiza la consulta para obtener usuarios y préstamos
$usuariosPrestamosQuery = "SELECT u.nombre_usuario, u.apellido, u.dni, u.saldo, 
                                p.id, p.cantidad, p.concepto, 'Pendiente' as estado 
                          FROM usuarios u
                          LEFT JOIN prestamos p ON u.nombre_usuario = p.nombre_usuario
                          WHERE u.es_administrador = 0";

$usuariosPrestamosResult = $conn->query($usuariosPrestamosQuery);

// Verifica si la consulta fue exitosa antes de intentar obtener resultados
if (!$usuariosPrestamosResult) {
    die("Error en la consulta: " . $conn->error);
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
                <a class="navbar-brand" href="indexadmin.php">IlerBank</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="administrar_prestamos.php">Ver Solicitudes de Préstamo</a>
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
        <div class="container" style="padding: 16px;">
            <h2>Panel de Administrador</h2>

            <h3>Lista de Usuarios y Préstamos</h3>
            <form method="post" action="procesar_prestamos.php">
    <table class="table">
        <!-- Cabecera de la tabla -->
        <thead>
            <tr>
                <!-- ... (otras columnas) ... -->
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($usuarioPrestamo = $usuariosPrestamosResult->fetch_assoc()) {
                echo "<tr>";
                // ... (columnas existentes) ...
                echo '<td>';
                echo '<input type="hidden" name="accion_prestamo" value="' . $usuarioPrestamo['id'] . '">';
                echo '<button type="submit" name="accion" value="aceptar">Aceptar</button>';
                echo '<button type="submit" name="accion" value="denegar">Denegar</button>';
                echo '</td>';
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</form>
        </div>
    </main>

<!-- ... (código existente) ... -->

</body>
</html>
