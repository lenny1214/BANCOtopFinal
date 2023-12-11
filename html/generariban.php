<?php
session_start();

// Verifica si el usuario no ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    header('Location: login.php');
    exit();
}

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    // Cierra la sesión y redirige a la página de inicio de sesión
    session_destroy();
    header('Location: login.php');
    exit();
}

// Crear la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilerbank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtén el email del usuario desde la base de datos
$nombreUsuario = $_SESSION['nombre_usuario'];
$sqlEmail = "SELECT email FROM usuarios WHERE nombre_usuario = '$nombreUsuario'";
$resultEmail = $conn->query($sqlEmail);

if ($resultEmail->num_rows > 0) {
    $row = $resultEmail->fetch_assoc();
    $emailUsuario = $row['email'];
   

    // Genera el IBAN
    $ibanUsuario = generarIBAN($emailUsuario);

    // Actualiza el campo iban en la base de datos
    $sqlUpdate = "UPDATE usuarios SET iban = '$ibanUsuario' WHERE nombre_usuario = '$nombreUsuario'";
    $resultadoUpdate = $conn->query($sqlUpdate);

    if (!$resultadoUpdate) {
        // Manejar el error de la consulta SQL según sea necesario
        echo 'Error al actualizar el IBAN en la base de datos.';
        exit();
    }
} else {
    // Manejar el caso en que no se encuentre el usuario
    echo 'Error: No se encontró el usuario.';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>IlerBank</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz"
        crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="versaldo.php">IlerBank</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <!-- Otros elementos del menú -->
                    </ul>
                </div>
                <form class="d-flex" method="post" action="">
                    <input class="btn btn-outline-danger" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
                </form>
            </div>
        </nav>
    </header>
    <main>
        <div class="container mt-3">
            <h2>Generar IBAN</h2>
            <p>IBAN generado: <?php echo $ibanUsuario; ?></p>
        </div>
    </main>

    <footer>
        <!-- Footer -->
    </footer>
</body>

</html>

<?php
// Función para generar el IBAN
function generarIBAN($email)
{
    $primerosCinco = substr($email, 0, 3);
    $codigoBinario = '';
    for ($i = 0; $i < strlen($primerosCinco); $i++) {
        $codigoBinario .= sprintf("%08b", ord($primerosCinco[$i]));
    }
    $iban = 'ES' . $codigoBinario . '0000';
    return $iban;
}
?>
