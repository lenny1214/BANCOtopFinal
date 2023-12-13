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

// Obtener todos los usuarios con sus saldos
$usuariosQuery = "SELECT nombre_usuario, apellido, dni, saldo FROM usuarios";
$usuariosResult = $conn->query($usuariosQuery);

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
                <a class="navbar-brand" href="indexadmin.php">
                    <img src="../img/logoBanco.png" alt="Logo del Banco" height="40" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="aprobar_prestamo.php">Ver Solicitudes de Préstamos</a>
                        </li>
                        
                <form class="d-flex" method="post" action="">
                    <input class="btn btn-outline-danger" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
                </form>
            </div>
        </nav>
    </header>

    <main>
        <div class="container" style="padding: 16px;">
            <h2>Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?> (Admin)</h2>

            <h2>Lista de Usuarios</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre de Usuario</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <th>Saldo</th> <!-- Nueva columna -->
                        <!-- Agregar más columnas según sea necesario -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($usuario = $usuariosResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$usuario['nombre_usuario']}</td>";
                        echo "<td>{$usuario['apellido']}</td>";
                        echo "<td>{$usuario['dni']}</td>";
                        echo "<td>{$usuario['saldo']}</td>"; 
                        // Agregar más columnas según sea necesario
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Resto del código HTML y Bootstrap -->

</body>

</html>
